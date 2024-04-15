<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];
// Измените SQL-запрос для выборки имени исполнителя.
$sql = "
SELECT 
    c.name AS song_title,
    MIN(s.liseningdate) AS first_listen_date,
    MAX(s.liseningdate) AS last_listen_date
FROM 
    statistic s
JOIN 
    audiofiles af ON s.audiofileid = af.audiofileid
JOIN 
    composition c ON af.audiofileid = c.compositionid
WHERE 
    customerid = ".$id."
GROUP BY 
    song_title
ORDER BY 
    song_title;";


$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'song_title' => $row['song_title'],
        'first_listen_date' => $row['first_listen_date'],
        'last_listen_date' => $row['last_listen_date']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Хронология</title>
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
    <?php echo '<h3 class="content-wrapper__text">Хронология</h3>'; ?>
    </div>
      <div id="chart_div" style="width: 900px; height: 500px;"></div>          
        </div>     
      </section>
        </div>
    </main>
</body>

<script type="text/javascript">
   // Загрузите библиотеку Google Charts
google.charts.load('current', {packages: ['timeline']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // Создайте массив данных
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Название песни');
    data.addColumn('date', 'Первое прослушивание песни');
    data.addColumn('date', 'Последнее прослушивание песни');

    // Добавьте данные
    <?php
    foreach ($data as $row) {
        $firstListenDate = strtotime($row['first_listen_date']);
        $lastListenDate = strtotime($row['last_listen_date']);
        echo "data.addRow(['" . $row['song_title'] . "', new Date($firstListenDate * 1000), new Date($lastListenDate * 1000)]);\n";
    }
    ?>

    var options = {
        timeline: { groupByRowLabel: true },
        height: 500,
    };

    // Создайте пузырьковую диаграмму и передайте опции
    var chart = new google.visualization.Timeline(document.getElementById('chart_div'));
    chart.draw(data, options);
}


</script>
</html>