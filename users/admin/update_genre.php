<?php
// update_genre.php

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получаем данные из массива POST
$genre = $_POST['genre'];
$id = $_POST['id'];

// Выполняем запрос к базе данных
$result = $conn->query("UPDATE genre SET name='" . $genre . "' WHERE genreid=" . $id);

// Отправляем ответ в формате JSON
echo json_encode(['success' => true]);
?>
