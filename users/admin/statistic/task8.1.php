<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ( $_GET['value'] == 1){
$sql = "
SELECT 
    DATE_FORMAT(s.listeningdate, '%Y-%m') AS listen_month, 
    COUNT(s.statisticid) AS listens_count
FROM 
    statistic s
GROUP BY 
    listen_month
ORDER BY 
    listen_month;";
}
else if ( $_GET['value'] == 2){
    $sql = " SELECT 
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
where g.genreid = 6
GROUP BY 
    g.name, listen_month
ORDER BY 
    listen_month, g.name;";
}
else{
    $sql = "
    SELECT 
    DATE_FORMAT(s.listeningdate, '%Y-%m') AS listen_month, 
    COUNT(s.statisticid) AS listens_count
FROM 
    statistic s
JOIN 
    audiofiles af ON s.audiofileid = af.audiofileid
JOIN 
    composition c ON af.audiofileid = c.compositionid
JOIN 
    artist a ON c.artistid = a.artistid
WHERE a.artistid = 13
GROUP BY 
    a.name, listen_month
ORDER BY 
    listen_month, a.name
    "; 
}

$result = $conn->query($sql);
$listens_data = [];

while ($row = $result->fetch_assoc()) {
    $listens_data[] = [
        'month' => $row['listen_month'],
        'count' => intval($row['listens_count'])
    ];
}

$conn->close();

function linearRegression($x, $y) {
    $n = count($x);
    $sumX = array_sum($x);
    $sumY = array_sum($y);
    $sumX2 = 0;
    $sumXY = 0;

    for ($i = 0; $i < $n; $i++) {
        $sumX2 += $x[$i] * $x[$i];
        $sumXY += $x[$i] * $y[$i];
    }

    $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
    $intercept = ($sumY - $slope * $sumX) / $n;

    return array($slope, $intercept);
}

function linearRegressionPredict($x, $slope, $intercept) {
    return $slope * $x + $intercept;
}

function movingAverageRecovery($data, $p) {
    $result = array();
    $count = count($data);

    // Аппроксимация краевых значений с помощью линейной регрессии
    $polyDegree = 2 * $p + 1;
    $firstValues = array_column(array_slice($data, 0, $polyDegree), 'count');
    $lastValues = array_column(array_slice($data, -$polyDegree), 'count');

    list($firstSlope, $firstIntercept) = linearRegression(range(1, $polyDegree), $firstValues);
    list($lastSlope, $lastIntercept) = linearRegression(range(1, $polyDegree), $lastValues);

    // Заполнение массива результатов
    for ($t = 0; $t < $count; $t++) {
        if ($t < $p || $t > $count - $p - 1) {
            // Если это краевые значения, используем аппроксимацию
            if ($t < $p) {
                $result[] = array('month' => $data[$t]['month'], 'moving_average' => linearRegressionPredict($t + 1, $firstSlope, $firstIntercept));
            } else {
                $result[] = array('month' => $data[$t]['month'], 'moving_average' => linearRegressionPredict($count - $t, $lastSlope, $lastIntercept));
            }
        } else {
            $start = max(0, $t - $p);
            $end = min($count - 1, $t + $p);
            $sum = array_sum(array_column(array_slice($data, $start, $end - $start + 1), 'count'));
            $result[] = array('month' => $data[$t]['month'], 'moving_average' => $sum / ($end - $start + 1));
        }
    }

    return $result;
}

function weightedMovingAverageRecovery($data, $weights) {
    $result = array();
    $count = count($data);
    $windowSize = count($weights);

    // Аппроксимация краевых значений с помощью линейной регрессии
    $polyDegree = floor($windowSize / 2) * 2 + 1;
    $firstValues = array_column(array_slice($data, 0, $polyDegree), 'count');
    $lastValues = array_column(array_slice($data, -$polyDegree), 'count');

    list($firstSlope, $firstIntercept) = linearRegression(range(1, $polyDegree), $firstValues);
    list($lastSlope, $lastIntercept) = linearRegression(range(1, $polyDegree), $lastValues);

    // Заполнение массива результатов
    for ($t = 0; $t < $count; $t++) {
        if ($t < floor($windowSize / 2) || $t > $count - floor($windowSize / 2) - 1) {
            // Если это краевые значения, используем аппроксимацию
            if ($t < floor($windowSize / 2)) {
                $result[] = array('month' => $data[$t]['month'], 'weighted_moving_average' => linearRegressionPredict($t + 1, $firstSlope, $firstIntercept));
            } else {
                $result[] = array('month' => $data[$t]['month'], 'weighted_moving_average' => linearRegressionPredict($count - $t, $lastSlope, $lastIntercept));
            }
        } else {
            $start = max(0, $t - floor($windowSize / 2));
            $end = min($count - 1, $t + floor($windowSize / 2));
            $sum = 0;
            for ($i = $start; $i <= $end; $i++) {
                $sum += $data[$i]['count'] * $weights[$i - $start];
            }
            $result[] = array('month' => $data[$t]['month'], 'weighted_moving_average' => $sum / array_sum($weights));
        }
    }

    return $result;
}

$window_sizes = [3, 7];
$moving_averages = [];

foreach ($window_sizes as $window_size) {
    $moving_averages[$window_size] = movingAverageRecovery($listens_data, floor($window_size / 2));
}

$weights = [-3, 12, 17, 12, -3];
$weighted_moving_averages = weightedMovingAverageRecovery($listens_data, $weights);


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/table.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/style_add.css">
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
                    <div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Месяц</th>
                <th>Количество прослушиваний</th>
                <th>Скользящее среднее (3 месяца)</th>
                <th>Взвешенное скользящее среднее (5 месяцев)</th>
                <th>Скользящее среднее (7 месяцев)</th>
            </tr>
        </thead>
        <tbody>
        <div id="chart_div" style="width: 900px; height: 500px;"></div>

            <?php foreach ($listens_data as $index => $data) { ?>
                <tr>
                    <td><?php echo $data['month']; ?></td>
                    <td><?php echo $data['count']; ?></td>
                    <td><?php echo isset($moving_averages[3][$index]['moving_average']) ? round($moving_averages[3][$index]['moving_average'], 2) : '—'; ?></td>
                    <td><?php echo isset($weighted_moving_averages[$index]['weighted_moving_average']) ? round($weighted_moving_averages[$index]['weighted_moving_average'], 2) : '—'; ?></td>
                    <td><?php echo isset($moving_averages[7][$index]['moving_average']) ? round($moving_averages[7][$index]['moving_average'], 2) : '—'; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
<script type="text/javascript">
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
            data.addColumn('string', 'Месяц');
            data.addColumn('number', 'Количество прослушиваний');
            data.addColumn('number', 'Скользящее среднее (3 месяца)');
            data.addColumn('number', 'Взвешенное скользящее среднее (5 месяцев)');
            data.addColumn('number', 'Скользящее среднее (7 месяцев)');
            data.addRows([
                <?php
                foreach ($listens_data as $index => $data) {
                    echo "['" . $data['month'] . "', " . ($data['count'] === '—' ? 'null' : $data['count']) . ", ";
                    echo isset($moving_averages[3][$index]['moving_average']) ? round($moving_averages[3][$index]['moving_average'], 2) : 'null';
                    echo ", ";
                    echo isset($weighted_moving_averages[$index]['weighted_moving_average']) ? round($weighted_moving_averages[$index]['weighted_moving_average'], 2) : 'null';
                    echo ", ";
                    echo isset($moving_averages[7][$index]['moving_average']) ? round($moving_averages[7][$index]['moving_average'], 2) : 'null';
                    echo "],";
                }
                ?>
            ]);

            var options = {
                title: 'Количество прослушиваний по месяцам',
                curveType: 'function',
                legend: { position: 'bottom' },
                series: {
                    1: { lineDashStyle: [14, 2, 7, 2] }, // Для линии скользящего среднего (3 месяца)
                    2: { lineDashStyle: [14, 2, 7, 2] }, // Для линии взвешенного скользящего среднего (5 месяцев)
                    3: { lineDashStyle: [14, 2, 7, 2] }  // Для линии скользящего среднего (7 месяцев)
                }
            };


        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
    }
</script>