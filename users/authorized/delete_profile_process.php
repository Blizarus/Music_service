<?php
session_start();

$id = $_SESSION['id'];

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Выполняем удаление
$conn->query("DELETE FROM customers WHERE customerid = " . $id);

// Очищаем сессию
session_unset();
session_destroy();

// Перенаправляем пользователя
header("Location: general_page.php");
exit;  
?>
