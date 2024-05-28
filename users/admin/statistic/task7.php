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
?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Задание 7</title>
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
        <table>
        <tr>
        <th rowspan="2">Месяц</th>
        <th rowspan="2">Прибыльность, тыс. руб.</th>
        <th colspan="2">Абсолютный прирост, тыс. руб.</th>
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
        <?php
        
        ?>
         
        </div>
      </section>
    </div>
  </main>
</body>

</html>