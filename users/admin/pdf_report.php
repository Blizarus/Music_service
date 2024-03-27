<?php
session_start();
require ($_SERVER['DOCUMENT_ROOT'] . '/libraries/tfpdf/tfpdf.php');

// Получение данных из POST-запроса
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];


$conn = new mysqli('music', 'root', '', 'music');

// Проверка соединения
if ($conn->connect_error) {
  die ("Connection failed: " . $conn->connect_error);
}

// Функция для создания таблицы с данными
function createTable($pdf, $title, $data)
{
  $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
  $pdf->SetFont('DejaVu', '', 12);
  $pdf->Cell(0, 10, $title, 0, 1, 'C');
  $pdf->Ln();
  $pdf->SetFont('DejaVu', '', 12);
  $pdf->Cell(40, 10, 'Месяц', 1, 0, 'C');
  $pdf->Cell(40, 10, 'Название', 1, 0, 'C');
  $pdf->Cell(80, 10, 'Количество прослушиваний', 1, 1, 'C');

  $prev_date = null;
  foreach ($data as $month => $rows) {
    $pdf->Cell(40, count($rows) * 10, $month, 1, 0, 'L');
    $pdf->Cell(40, 0, '', 0, 0, 'L'); // Пустая ячейка для названия
    $pdf->Cell(40, 0, '', 0, 1, 'L'); // Пустая ячейка для количества прослушиваний
    foreach ($rows as $row) {
      // Вывод данных о прослушиваниях
      $pdf->Cell(40, 10, '', 0, 0, 'L');
      $pdf->Cell(40, 10, $row['Name'], 1, 0, 'L');
      $pdf->Cell(80, 10, $row['Listens'], 1, 1, 'C');
    }
  }
  $pdf->Ln();
}

// SQL-запросы для получения данных о прослушиваниях за выбранный период с группировкой по месяцам
$sql_genre = "SELECT 
                DATE_FORMAT(liseningdate, '%Y-%m') AS Month,
                genre.name AS Name,
                COUNT(*) AS Listens 
              FROM 
                statistic 
              JOIN 
                composition ON statistic.audiofileid = composition.compositionid 
              JOIN 
                genre ON composition.genreid = genre.genreid 
              WHERE 
                liseningdate BETWEEN '$start_date' AND '$end_date' 
              GROUP BY 
                Month, genre.name 
              ORDER BY 
                Month, Listens DESC";

$sql_artist = "SELECT 
                DATE_FORMAT(liseningdate, '%Y-%m') AS Month,
                artist.name AS Name,
                COUNT(*) AS Listens 
              FROM 
                statistic 
              JOIN 
                composition ON statistic.audiofileid = composition.compositionid 
              JOIN 
                artist ON composition.artistid = artist.artistid 
              WHERE 
                liseningdate BETWEEN '$start_date' AND '$end_date' 
              GROUP BY 
                Month, artist.name 
              ORDER BY 
                Month, Listens DESC";

$sql_track = "SELECT 
                DATE_FORMAT(liseningdate, '%Y-%m') AS Month,
                composition.name AS Name,
                COUNT(*) AS Listens 
              FROM 
                statistic 
              JOIN 
                composition ON statistic.audiofileid = composition.compositionid 
              WHERE 
                liseningdate BETWEEN '$start_date' AND '$end_date' 
              GROUP BY 
                Month, composition.name 
              ORDER BY 
                Month, Listens DESC";

$result_genre = $conn->query($sql_genre);
$result_artist = $conn->query($sql_artist);
$result_track = $conn->query($sql_track);

// Создание PDF
$pdf = new tFPDF();
$pdf->AddPage();

// Создание таблиц для каждого рейтинга
$data_genre = [];
$data_artist = [];
$data_track = [];

// Обработка результатов запроса для жанров
while ($row = $result_genre->fetch_assoc()) {
  $month = $row['Month'];
  unset($row['Month']);
  $data_genre[$month][] = $row;
}

// Обработка результатов запроса для исполнителей
while ($row = $result_artist->fetch_assoc()) {
  $month = $row['Month'];
  unset($row['Month']);
  $data_artist[$month][] = $row;
}

// Обработка результатов запроса для песен
while ($row = $result_track->fetch_assoc()) {
  $month = $row['Month'];
  // unset($row['Month']);
  $data_track[$month][] = $row;
}

// Вывод таблиц для каждого рейтинга
createTable($pdf, 'Рейтинг жанров по прослушиваниям', $data_genre);
$pdf->Ln();

createTable($pdf, 'Рейтинг исполнителей по прослушиваниям', $data_artist);
$pdf->Ln();

createTable($pdf, 'Рейтинг песен по прослушиваниям', $data_track);
$pdf->Ln();


// Получение данных о самом популярном жанре
$sql_most_popular_genre = "SELECT 
                                genre.name AS Name,
                                COUNT(*) AS Listens 
                            FROM 
                                statistic 
                            JOIN 
                                composition ON statistic.audiofileid = composition.compositionid 
                            JOIN 
                                genre ON composition.genreid = genre.genreid 
                            WHERE 
                                liseningdate BETWEEN '$start_date' AND '$end_date' 
                            GROUP BY 
                                genre.name 
                            ORDER BY 
                                Listens DESC 
                            LIMIT 1";

$result_most_popular_genre = $conn->query($sql_most_popular_genre);
$most_popular_genre = $result_most_popular_genre->fetch_assoc();

// Получение данных о самом популярном исполнителе
$sql_most_popular_artist = "SELECT 
                                artist.name AS Name,
                                COUNT(*) AS Listens 
                            FROM 
                                statistic 
                            JOIN 
                                composition ON statistic.audiofileid = composition.compositionid 
                            JOIN 
                                artist ON composition.artistid = artist.artistid 
                            WHERE 
                                liseningdate BETWEEN '$start_date' AND '$end_date' 
                            GROUP BY 
                                artist.name 
                            ORDER BY 
                                Listens DESC 
                            LIMIT 1";

$result_most_popular_artist = $conn->query($sql_most_popular_artist);
$most_popular_artist = $result_most_popular_artist->fetch_assoc();

// Получение данных о самой популярной песне
$sql_most_popular_track = "SELECT 
                                composition.name AS Name,
                                COUNT(*) AS Listens 
                            FROM 
                                statistic 
                            JOIN 
                                composition ON statistic.audiofileid = composition.compositionid 
                            WHERE 
                                liseningdate BETWEEN '$start_date' AND '$end_date' 
                            GROUP BY 
                                composition.name 
                            ORDER BY 
                                Listens DESC 
                            LIMIT 1";

$result_most_popular_track = $conn->query($sql_most_popular_track);
$most_popular_track = $result_most_popular_track->fetch_assoc();

// Вывод данных о самых популярных жанре, исполнителе и песне
$pdf->SetFont('DejaVu', '', 12);
$pdf->Ln();
$pdf->Cell(0, 10, 'Самый популярный жанр: ' . $most_popular_genre['Name'] . ' (' . $most_popular_genre['Listens'] . ' прослушиваний)', 0, 1, 'C');
$pdf->Cell(0, 10, 'Самый популярный исполнитель: ' . $most_popular_artist['Name'] . ' (' . $most_popular_artist['Listens'] . ' прослушиваний)', 0, 1, 'C');
$pdf->Cell(0, 10, 'Самая популярная песня: ' . $most_popular_track['Name'] . ' (' . $most_popular_track['Listens'] . ' прослушиваний)', 0, 1, 'C');

// Вывод PDF
$pdf->Output();

// Вывод PDF
$pdf->Output();

