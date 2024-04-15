<?php
session_start();

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// Получение данных из POST-запроса
$login = $_POST['login'];
$password = $_POST['password'];
$name = $_POST['name'];
$lname = $_POST['lname'];
$email = $_POST['email'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array('status' => 'invalid_email'));
    exit;
}

// Поиск пользователя в базе данных
$sql1 = "select * from customers where email = '$email'";
$result1 = $conn->query($sql1);
if ($result1->num_rows > 0) {
    echo json_encode(array('status' => 'exists'));
    exit;
}

$sql = "select * from login_password where login = '$login' and password = sha1('$password')";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo json_encode(array('status' => 'change'));
    exit;
}

$conn->query("INSERT INTO customers VALUES (NULL, '$name', '$lname', '$email', 0, '')");
$insert_id = $conn->insert_id;

$conn->query("INSERT INTO login_password VALUES (NULL, '$login', SHA1('$password'))");

$_SESSION['login'] = $login;
$_SESSION['id'] = $insert_id;

echo json_encode(['status' => 'success']);

$conn->close();
exit;
?>