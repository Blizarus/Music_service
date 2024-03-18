<?php

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Проверяем, был ли успешно загружен файл
if ($_FILES['imageData']['error'] === UPLOAD_ERR_OK) {
    $tempFile = $_FILES['imageData']['tmp_name'];
    $genreId = $_GET['id'];
    $targetFile = "C:\\WebServers\\home\\music\\wwwmedia\\genre\\"  . $genreId . ".png";

    // Перемещаем временный файл в целевой каталог
    if (move_uploaded_file($tempFile, $targetFile)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Ошибка перемещения файла']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Ошибка при загрузке файла']);
}
?>
