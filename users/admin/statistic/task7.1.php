<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Поиск пользователя в базе данных
$sql = "SELECT DATE_FORMAT(listeningdate, '%Y-%m') AS month_year, COUNT(statisticid) AS listens
FROM statistic
GROUP BY month_year
ORDER BY month_year";
$result = $conn->query($sql);

// Создание массива данных для графика
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = array($row['month_year'], intval($row['listens']));
}

// Закрытие соединения с базой данных
$conn->close();
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График прослушиваний</title>
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
                <strong>Остаток</strong>: Случайные колебания, не объясняемые трендом или сезонностью.
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
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Месяц');
        data.addColumn('number', 'Прослушивания');
        data.addRows(<?php echo json_encode($data); ?>);

        var options = {
            title: 'График прослушиваний организации по месяцам',
            hAxis: {
                title: 'Месяц',
                textStyle: {
                    color: '#FFFFFF' // Установка цвета текста по оси X
                },
                titleTextStyle: {
                    color: '#FFFFFF' // Установка цвета заголовка оси X
                }
            },
            vAxis: {
                title: 'Прослушивания',
                textStyle: {
                    color: '#FFFFFF' // Установка цвета текста по оси Y
                },
                titleTextStyle: {
                    color: '#FFFFFF' // Установка цвета заголовка оси Y
                }
            },
            width: 1000, // Установка ширины графика
            height: 800, // Установка высоты графика
            backgroundColor: 'transparent', // Установка цвета фона 
            colors: ['#FFA500'], // Установка цвета линии графика (оранжевый)
            legend: { textStyle: { color: '#FFFFFF' } }, // Установка цвета текста легенды
            titleTextStyle: { color: '#FFFFFF' } // Установка цвета заголовка графика
        };


        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>

</html>