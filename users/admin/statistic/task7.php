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

// Создание массива данных
$data = array();
while ($row = $result->fetch_assoc()) {
  $data[] = array('month_year' => $row['month_year'], 'listens' => intval($row['listens']));
}

// Функция для вычисления абсолютного прироста и темпов роста
function calculateGrowth($data) {
    $previousListens = null;
    $baseListens = isset($data[0]['listens']) ? $data[0]['listens'] : 0;

    foreach ($data as $key => $row) {
        if ($previousListens !== null) {
            $data[$key]['absolute_growth_chain'] = $row['listens'] - $previousListens; //прирост цепной 
            $data[$key]['absolute_growth_basic'] = $row['listens'] - $baseListens; //прирост базисный

            $data[$key]['growth_rate_chain'] = ($previousListens != 0) ? (($row['listens'] - $previousListens) / $previousListens) * 100 : 0; //темп роста цепной
            $data[$key]['growth_rate_basic'] = ($baseListens != 0) ? (($row['listens'] - $baseListens) / $baseListens) * 100 : 0; //темп роста базисный
        
            $data[$key]['increase_rate_chain'] = $data[$key]['growth_rate_chain'] - 100;
            $data[$key]['increase_rate_basic'] = $data[$key]['absolute_growth_basic'] - 100;

          } else {
            $data[$key]['absolute_growth_chain'] = '-';
            $data[$key]['absolute_growth_basic'] = '-';

            $data[$key]['growth_rate_basic'] = '-';
            $data[$key]['growth_rate_chain'] = '-';

            $data[$key]['increase_rate_chain'] = '-';
            $data[$key]['increase_rate_basic'] = '-';
        }

        $previousListens = $row['listens'];
    }

    return $data;
}

$data = calculateGrowth($data);
?>



<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Задание 7</title>
  <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/table.css">
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
        <table>
        <tr>
<th rowspan="2">Месяц</th>
<th rowspan="2">Количество прослушиваний</th>
<th colspan="2">Абсолютный прирост, количество</th>
<th colspan="2">Темп роста (%)</th>
<th colspan="2">Темп прироста (%)</th>
</tr>
<tr>
<th>Цепной</th>
<th>Базисный</th>
<th>Цепной</th>
<th>Базисный</th>
<th>Цепной</th>
<th>Базисный</th>
</tr>

    <?php foreach ($data as $row) { ?>
        <tr>
            <td><?= date('m/Y', strtotime($row['month_year'])) ?></td>
            <td><?= number_format($row['listens'], 0, ',', ' ') ?></td>
            <td><?= is_numeric($row['absolute_growth_chain']) ? number_format($row['absolute_growth_chain'], 0, ',', ' ') : $row['absolute_growth_chain'] ?></td>
            <td><?= is_numeric($row['absolute_growth_basic']) ? number_format($row['absolute_growth_basic'], 0, ',', ' ') : $row['absolute_growth_basic'] ?></td>

            <td><?= is_numeric($row['growth_rate_basic']) ? number_format($row['growth_rate_basic'], 2, ',', ' ') : $row['growth_rate_basic'] ?></td>
            <td><?= is_numeric($row['growth_rate_chain']) ? number_format($row['growth_rate_chain'], 2, ',', ' ') : $row['growth_rate_chain'] ?></td>

            <td><?= is_numeric($row['increase_rate_chain']) ? number_format($row['increase_rate_chain'], 2, ',', ' ') : $row['increase_rate_chain'] ?></td>
            <td><?= is_numeric($row['increase_rate_basic']) ? number_format($row['increase_rate_basic'], 2, ',', ' ') : $row['increase_rate_basic'] ?></td>
        </tr>
    <?php } 
    $conn->close();
    ?>
</table>       
        </div>
      </section>
    </div>
  </main>
</body>

</html>