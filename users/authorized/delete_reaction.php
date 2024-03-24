<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Подключение к базе данных
    $conn = new mysqli('music', 'root', '', 'music');

    // Проверка подключения
    if ($conn->connect_error) {
        die ("Connection failed: " . $conn->connect_error);
    }

    // Получение данных из формы
    $customerid = $_SESSION['id']; // Предполагается, что id пользователя хранится в сессии
    $messageid = $_POST['messageId'];

    // Подготовленный запрос на удаление реакции пользователя
    $stmt = $conn->prepare("DELETE FROM reactions WHERE messageid = ? AND customerid = ?");
    $stmt->bind_param("ii", $messageid, $customerid);

    // Выполнение запроса
    if ($stmt->execute()) {
        // Реакция успешно удалена
        echo "Реакция успешно удалена.";
    } else {
        // В случае ошибки
        echo "Ошибка при удалении реакции: " . $conn->error;
    }

    // Закрытие соединения с базой данных
    $stmt->close();
    $conn->close();
} else {
    echo "Ошибка: неверный метод запроса";
}
?>
