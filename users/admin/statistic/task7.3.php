<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT 
    g.name AS genre_name, 
    DATE_FORMAT(s.listeningdate, '%Y-%m') AS listen_month, 
    COUNT(s.statisticid) AS listens_count
FROM 
    statistic s
JOIN 
    audiofiles af ON s.audiofileid = af.audiofileid
JOIN 
    composition c ON af.audiofileid = c.compositionid
JOIN 
    genre g ON c.genreid = g.genreid
GROUP BY 
    g.name, listen_month
ORDER BY 
    listen_month, g.name;";

$result = $conn->query($sql);
$data = [];
$genres = [];

while ($row = $result->fetch_assoc()) {
    $month = $row['listen_month'];
    $genre = $row['genre_name'];
    $count = intval($row['listens_count']);

    if (!in_array($genre, $genres)) {
        $genres[] = $genre;
    }

    if (!isset($data[$month])) {
        $data[$month] = array_fill_keys($genres, 0);
    }

    $data[$month][$genre] = $count;
}

$conn->close();

echo '<script type="text/javascript">';
echo 'var chartData = [[';
echo '\'Месяц\', ';
foreach ($genres as $genre) {
    echo '\'' . $genre . '\', ';
}
echo '],';
foreach ($data as $month => $counts) {
    echo '[\'' . $month . '\', ';
    foreach ($genres as $genre) {
        $count = isset($counts[$genre]) ? $counts[$genre] : 0;
        echo $count . ', ';
    }
    echo '],';
}
echo '];';
echo '</script>';
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диаграмма областей прослушиваний по жанрам</title>
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
                <p class="content-news_description"><strong>Тренд</strong>: Вероятно, будет показывать устойчивый рост с течением времени, так как общее количество прослушиваний увеличивается. <br>
                <strong>Сезонность</strong>: Будет выражена в виде пиков в зимние месяцы (декабрь и январь), связано с зимними праздниками. <br>
                <strong>Остаток</strong>: Случайные колебания, возможная популярность в различных соцсетях.
                    </p>
                    <div id="chart_div" style="width: 900px; height: 500px;"></div>
                </div>
            </section>
        </div>
    </main>
</body>

<script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable(chartData);

        var options = {
            title: 'Диаграмма областей прослушиваний по жанрам',
            hAxis: {
                title: 'Месяц',
                textStyle: { color: '#FFFFFF' },
                titleTextStyle: { color: '#FFFFFF' }
            },
            vAxis: {
                title: 'Прослушивания',
                textStyle: { color: '#FFFFFF' },
                titleTextStyle: { color: '#FFFFFF' },
                minValue: 0
            },
            chartArea: { width: '50%', height: '70%' },
            width: 1200, // Установка ширины графика
            height: 800, // Установка высоты графика
            backgroundColor: 'transparent',
            legend: { textStyle: { color: '#FFFFFF' } }, // Установка цвета текста легенды
            titleTextStyle: { color: '#FFFFFF' } // Установка цвета заголовка графика
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>

</html>
