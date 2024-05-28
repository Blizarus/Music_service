<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT 
    DATE(s.listeningdate) AS listen_date, 
    COUNT(s.statisticid) AS listens_count
FROM 
    statistic s
GROUP BY 
    listen_date
ORDER BY 
    listen_date ASC;";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'listen_date' => $row['listen_date'],
        'listens_count' => intval($row['listens_count'])
    ];
}

// Формируем массив данных для JavaScript
$json_data = json_encode($data);
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Календарь прослушиваний</title>
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
    // Загружаем библиотеку календаря
    google.charts.load('current', { 'packages': ['calendar'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        // Подготовка данных
        var data = new google.visualization.DataTable();
        data.addColumn('date', 'Дата');
        data.addColumn('number', 'Прослушивания песен');

        // Добавляем данные в таблицу из PHP массива
        var jsonData = <?php echo $json_data; ?>;
        jsonData.forEach(row => {
            var date = new Date(row['listen_date']);
            var listensCount = row['listens_count'];
            data.addRow([date, listensCount]);
        });

        // Настройка опций для календаря
        var options = {
            title: 'Календарь прослушиваний песен',
            cellSize: 16,
            noDataPattern: {
                backgroundColor: '#76a7fa',
                color: '#a0c3ff'
            },
            width: 1200,
            height: 800,
            underYearSpace: 10
        };

        // Создаем календарь и отображаем его на странице
        var calendar = new google.visualization.Calendar(document.getElementById('chart_div'));
        calendar.draw(data, options);
    }
</script>

</html>