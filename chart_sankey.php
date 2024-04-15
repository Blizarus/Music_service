<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT 
    s.customerid AS client,
    c.name AS song,
    COUNT(s.statisticid) AS weight
FROM 
    statistic s
JOIN 
    composition c ON s.audiofileid = c.compositionid
GROUP BY 
    client, song
ORDER BY 
    client, song;";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'source' => $row['client'], // клиент
        'target' => $row['song'], // песня
        'weight' => intval($row['weight'])
    ];
}
    
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диаграмма Санки</title>
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
    <?php echo '<h3 class="content-wrapper__text">Диаграмма Санки</h3>'; ?>
    </div>
      <div id="chart_div" style="width: 900px; height: 500px;"></div>          
        </div>     
      </section>
        </div>
    </main>
</body>

<script type="text/javascript">
   // Загрузите библиотеку Google Charts
google.charts.load('current', {packages: ['sankey']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
    // Создайте массив данных для диаграммы Санки
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Источник');
    data.addColumn('string', 'Цель');
    data.addColumn('number', 'Прослушиваний');

    // Добавьте данные
    <?php
    foreach ($data as $row) {
        echo "data.addRow(['" . $row['source'] . "', '" . $row['target'] . "', " . $row['weight'] . "]);\n";
    }
    ?>

    // Настройки диаграммы Санки
    var options = {
        width: 900,
        height: 500,
        sankey: {
            node: {
                colors: ['#76A7FA', '#34A853', '#EA4335', '#FBBC05', '#A7A9AC']
            }
        }
    };

    // Создайте диаграмму и передайте данные и опции
    var chart = new google.visualization.Sankey(document.getElementById('chart_div'));
    chart.draw(data, options);
}

</script>
</html>