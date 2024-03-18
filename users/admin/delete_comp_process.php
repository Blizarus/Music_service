<?php
$compositionid = $_GET['id'];

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "select * from audiofiles where audiofileid=".$compositionid."";
$result = $conn->query($sql);
$composition = $result->fetch_row();

if (file_exists($composition[2])) {
    // Пробуем удалить файл
    if (unlink($composition[2])) {
    } else {
        echo 'Ошибка при удалении файла.';
    }
} else {
    echo 'Файл не существует.';
}

if (file_exists($composition[4])) {
    // Пробуем удалить файл
    if (unlink($composition[4])) {
    } else {
        echo 'Ошибка при удалении файла.';
    }
} else {
    echo 'Файл не существует.';
}
$conn->query("delete from audiofiles where audiofileid=".$compositionid."");

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>