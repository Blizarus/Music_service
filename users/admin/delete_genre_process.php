<?php
$genreid = $_GET['id'];

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "select * from genre where genreid=".$genreid."";
$result = $conn->query($sql);
$composition = $result->fetch_row();

if (file_exists($composition[2])) {
    // Пробуем удалить файл
    if (unlink($composition[2])) {
    } 
} 
$conn->query("delete from genre where genreid=".$genreid."");

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>