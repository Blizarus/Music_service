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
  $reaction = $_POST['reactionType'];

  // Подготовка и выполнение запроса на добавление сообщения
  $stmt = $conn->prepare("INSERT INTO reactions (messageid, customerid, reaction) VALUES (?, ?, ?)");
  if ($stmt) {
    $stmt->bind_param("iii", $messageid, $customerid, $reaction);
    $stmt->execute();
    $stmt->close();

    $conn->close();
  } else {
    die ("Error in reactions query: " . $conn->error);
  }

} else {
  echo "Ошибка: неверный метод запроса";
}
?>