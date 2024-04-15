<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_max_users = "
SELECT COUNT(DISTINCT customerid) AS max_users
FROM  customers;";

$result_max_users = $conn->query($sql_max_users);
$max_users = 0;
if ($result_max_users->num_rows > 0) {
    $row = $result_max_users->fetch_assoc();
    $max_users = intval($row['max_users']);
}

$sql_max_listens = "
SELECT MAX(listen_count) AS max_listens
FROM (
    SELECT COUNT(s.statisticid) AS listen_count
    FROM statistic s
    GROUP BY audiofileid
) AS grouped_data;";

$result_max_listens = $conn->query($sql_max_listens);
$max_listens = 0;
if ($result_max_listens->num_rows > 0) {
    $row = $result_max_listens->fetch_assoc();
    $max_listens = intval($row['max_listens']) + 1;
}

$sql = "
SELECT 
    a.name AS artist_name,
    c.name AS song_title, 
    COUNT(s.statisticid) AS listens_count, 
    COUNT(DISTINCT s.customerid) AS user_count
FROM 
    statistic s
JOIN 
    composition c ON s.audiofileid = c.compositionid
JOIN 
    artist a ON c.artistid = a.artistid
GROUP BY 
    song_title
ORDER BY 
    song_title;";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'song_title' => $row['song_title'],
        'listens_count' => intval($row['listens_count']),
        'user_count' => intval($row['user_count']),
        'artist_name' => $row['artist_name']
    ];
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диаграмма областей прослушиваний исполнителей</title>
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
    data.addColumn('string', 'Название песни');
    data.addColumn('number', 'Прослушивания песен');
    data.addColumn('number', 'Уникальные пользователи');
    data.addColumn('string', 'Исполнитель');

    <?php
    foreach ($data as $row) {
         echo "data.addRow(['".$row['song_title']."', ".$row['listens_count']." , ".$row['user_count'].", '". $row['artist_name']."']);\n";
    }
    ?>

    var options = {
        title: 'Пузырьковая диаграмма прослушиваний песен',
        backgroundColor: 'transparent', 
        hAxis: {title: 'Прослушивания песен',maxValue: <?php echo $max_listens; ?>, textStyle: {color: '#FFFFFF'}, titleTextStyle: {color: '#FFFFFF'}, },
        vAxis: {title: 'Уникальные пользователи',maxValue: <?php echo $max_users; ?>, textStyle: {color: '#FFFFFF'}, titleTextStyle: {color: '#FFFFFF'}, },
        bubble: {textStyle: {fontSize: 11}},
        legend: {textStyle: {color: '#FFFFFF'}}, // Установка цвета текста легенды
        titleTextStyle: {color: '#FFFFFF'} // Установка цвета заголовка графика
    };

    var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}

</script>

</html>
