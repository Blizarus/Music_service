<?php
session_start();

require ($_SERVER['DOCUMENT_ROOT'] . '/libraries/tfpdf/tfpdf.php');

$conn = new mysqli('music', 'root', '', 'music');

// Проверка соединения
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Запрос к базе данных для получения данных
$id = $_SESSION['id']; // Пример: id пользователя
$sql = "SELECT listeningdate, 
               (SELECT name FROM artist WHERE artist.artistid = (SELECT artistid FROM composition WHERE composition.compositionid = statistic.audiofileid)) AS Artist,
               (SELECT name FROM composition WHERE composition.compositionid = statistic.audiofileid) AS Name
        FROM statistic WHERE customerid = " . $id;
$result = $conn->query($sql);

// Создание PDF
$pdf = new tFPDF();
$pdf->AddPage();

$pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
$pdf->SetFont('DejaVu', '', 12);

$pdf->Cell(0, 10, 'История прослушивания композиций', 0, 1, 'C');
// Заголовок таблицы
$pdf->Cell(50, 10, 'Дата прослушивания', 1);
$pdf->Cell(60, 10, 'Исполнитель', 1);
$pdf->Cell(60, 10, 'Название', 1);
$pdf->Ln();

// Добавление данных из базы данных в таблицу PDF
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50, 10, $row["listeningdate"], 1);
    $pdf->Cell(60, 10, $row["Artist"], 1);
    $pdf->Cell(60, 10, $row["Name"], 1);
    $pdf->Ln();
  }
} else {
  $pdf->Cell(190, 10, 'Нет данных', 1, 0, 'C');
}
$sql = "SELECT 
            genre.name AS Most_Listened_Genre,
            artist.name AS Most_Listened_Artist,
            composition.name AS Most_Listened_Track
        FROM 
            statistic
        JOIN 
            composition ON statistic.audiofileid = composition.compositionid
        JOIN 
            artist ON composition.artistid = artist.artistid
        JOIN 
            genre ON composition.genreid = genre.genreid
        WHERE 
            statistic.customerid = " . $id . "
        GROUP BY 
            composition.genreid
        ORDER BY 
            COUNT(*) DESC
        LIMIT 1";
$result = $conn->query($sql);

// Создание PDF
$pdf->AddPage();

// Добавление заголовка
$pdf->Cell(0, 10, 'Самый прослушиваемый жанр, исполнитель и трек', 0, 1, 'C');

// Добавление данных из базы данных в PDF
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $pdf->Cell(0, 10, 'Жанр: ' . $row["Most_Listened_Genre"], 0, 1);
    $pdf->Cell(0, 10, 'Исполнитель: ' . $row["Most_Listened_Artist"], 0, 1);
    $pdf->Cell(0, 10, 'Трек: ' . $row["Most_Listened_Track"], 0, 1);
  }
} else {
  $pdf->Cell(0, 10, 'Нет данных', 0, 1, 'C');
}

// Закрытие соединения с базой данных
$conn->close();

// Вывод PDF
$pdf->Output();
?>