<?php
session_start();

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// Получение данных из POST-запроса
$login = $_POST['login'];
$password = $_POST['password'];

// Поиск пользователя в базе данных

$sql = "select * from login_password where login = '$login' and password = sha1('$password')";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $Data = $result->fetch_row();
    $id = $Data[0];
    $result = $conn->query("SELECT administrator FROM customers WHERE customerid = $id");
    $Data = $result->fetch_row();
    $_SESSION['login'] = $login;
    $_SESSION['id'] = $id;
    $_SESSION['right'] = $Data[0];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>