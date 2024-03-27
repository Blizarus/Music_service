<?php
require ($_SERVER['DOCUMENT_ROOT'] . '/libraries/tfpdf/tfpdf.php');

$conn = new mysqli('music', 'root', '', 'music');

// Проверка соединения
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// Запрос к базе данных для получения данных о прослушиваниях по жанрам
$sql = "SELECT 
            genre.name AS Genre,
            artist.name AS Artist,
            COUNT(*) AS Listens
        FROM 
            statistic
        JOIN 
            composition ON statistic.audiofileid = composition.compositionid
        JOIN 
            artist ON composition.artistid = artist.artistid
        JOIN 
            genre ON composition.genreid = genre.genreid
        GROUP BY 
            genre.genreid, artist.artistid
        ORDER BY 
            genre.name, Listens DESC";

$result = $conn->query($sql);

// Создание PDF
$pdf = new tFPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
$pdf->SetFont('DejaVu', '', 12);

// Переменная для хранения текущего жанра
$current_genre = '';

// Обработка результатов запроса и создание таблиц для каждого жанра
while ($row = $result->fetch_assoc()) {
    // Проверка на смену жанра
    if ($row['Genre'] != $current_genre) {
        // Вывод заголовка для нового жанра
        $pdf->Ln();
        $pdf->Cell(0, 10, 'Жанр: ' . $row['Genre'], 0, 1, 'L');
        $pdf->Ln();
        // Заголовок таблицы
        $pdf->Cell(50, 10, 'Исполнитель', 1, 0, 'C');
        $pdf->Cell(80, 10, 'Количество прослушиваний', 1, 1, 'C');
        // Обновление текущего жанра
        $current_genre = $row['Genre'];
    }
    // Вывод данных об исполнителе и количестве прослушиваний
    $pdf->Cell(50, 10, $row['Artist'], 1, 0, 'L');
    $pdf->Cell(80, 10, $row['Listens'], 1, 1, 'C');
}

// Закрытие соединения с базой данных
$conn->close();

// Вывод PDF
$pdf->Output();
?>