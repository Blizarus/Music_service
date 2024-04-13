<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// Поиск пользователя в базе данных
$sql = "SELECT 
a.name AS artist_name,
DATE_FORMAT(s.liseningdate, '%Y-%m') AS listen_month,
COUNT(s.statisticid) AS listens_count
FROM 
statistic s
JOIN 
audiofiles af ON s.audiofileid = af.audiofileid
JOIN 
composition c ON af.audiofileid = c.compositionid
JOIN 
artist a ON c.artistid = a.artistid
GROUP BY 
artist_name, listen_month
ORDER BY 
listen_month, artist_name;";
$result = $conn->query($sql);

// Создание массива данных для графика
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array($row['listen_month'], intval($row['listens_count']));
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
    // echo '<h3 class="content-wrapper__text">' . $name_artist . '</h3>'; ?>
    </div>
        <div class="content-main">

      <div id="chart_div" style="width: 900px; height: 500px;"></div>          
        </div>     
      </section>
        </div>
    </main>
</body>
<script>
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Month', 'Artist Listens'],
      <?php
        foreach ($data as $row) {
          echo "['" . $row[0] . "', " . $row[1] . "],";
        }
      ?>
    ]);

    var options = {
      title: 'Artist Listens Over Time',
      hAxis: {title: 'Month'},
      vAxis: {title: 'Listens'},
      pointSize: 5,
      pointShape: 'circle',
      legend: { position: 'top' }
    };

    var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
</script>

</html>