<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT 
    g.name AS genre,
    a.name AS artist,
    c.name AS song
FROM 
    composition c
JOIN 
    artist a ON c.artistid = a.artistid
JOIN 
    genre g ON c.genreid = g.genreid
ORDER BY 
    genre, artist, song;";

$result = $conn->query($sql);
$data = [];

// Сбор данных в массив
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'genre' => $row['genre'],
        'artist' => $row['artist'],
        'song' => $row['song']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Организационная диаграмма</title>
    <link rel="stylesheet" href="/style.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
  <header class="header">
        <a href="../general_page.php">Музыкальный сервис</a>
    </header> 
    <main class="main">
        <div class="container">
        <?php require_once ($_SERVER['DOCUMENT_ROOT'] . '/settings.php'); ?>

        <section class="content">
        <div class="content-main">
    <div class="content-head">
    <?php echo '<h3 class="content-wrapper__text">Организационная диаграмма</h3>'; ?>
    </div>
      <div id="chart_div" style="width: 900px; height: 500px;"></div>          
        </div>     
      </section>
        </div>
    </main>
</body>

<script type="text/javascript">
    // Загружаем библиотеку Google Charts
    google.charts.load('current', {'packages':['orgchart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Создаем массив данных
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Node ID');
        data.addColumn('string', 'Node Parent');
        data.addColumn('string', 'Tooltip');
        
        // Добавляем данные из PHP-части
        <?php
        foreach ($data as $row) {
            echo "data.addRow(['" . $row['song'] . "', '" . $row['artist'] . "', '" . $row['song'] . "']);\n";
            echo "data.addRow(['" . $row['artist'] . "', '" . $row['genre'] . "', '" . $row['artist'] . "']);\n";
        }
        ?>
        // Создайте объект организации и передайте данные
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        chart.draw(data, {allowHtml: true});
    }
</script>
</html>