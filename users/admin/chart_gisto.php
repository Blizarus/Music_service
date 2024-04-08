<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// Поиск пользователя в базе данных
$name_artist = $_GET['name'];
$artistid = $_GET['id'];
$sql = "SELECT c.name AS composition_name, COUNT(s.statisticid) AS listens, a.name
FROM composition c
JOIN statistic s ON c.compositionid = s.audiofileid
JOIN artist a ON c.artistid = a.artistid
WHERE c.artistid  = ".$artistid."
GROUP BY c.name;";
$result = $conn->query($sql);
// Создание массива данных для гистограммы
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array($row['composition_name'], intval($row['listens']));
}

// Закрытие соединения с базой данных
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Гистограмма прослушиваний</title>
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
        <div class="content-head">
    <?php 
    echo '<h3 class="content-wrapper__text">' . $name_artist . '</h3>'; ?>
    </div>
        <div class="content-main">

      <div id="chart_div" style="width: 900px; height: 500px;"></div>          
        </div>     
      </section>
        </div>
    </main>
</body>

<script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Песня');
    data.addColumn('number', 'Прослушивания');
    data.addRows(<?php echo json_encode($data); ?>);

    var options = {
        title: 'Прослушивания песен исполнителя за все время',
        legend: { position: 'none' },
        backgroundColor: 'transparent', // Цвет фона
        titleTextStyle: {
            color: '#FFFFFF' // Цвет заголовка
        },
        hAxis: {
            textStyle: {
                color: '#FFFFFF' // Цвет текста по горизонтали
            }
        },
        vAxis: {
            textStyle: {
                color: '#FFFFFF' // Цвет текста по вертикали
            }
        },
        colors: ['#8DCFFF'], // Цвет графика
    };

    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}

    </script>
</html>
