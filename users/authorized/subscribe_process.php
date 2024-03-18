<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];

$mailingid = $_GET['id'];

$stmt = $conn->prepare("INSERT INTO subscriptions VALUES (?, ?)");
if ($stmt) {
  $stmt->bind_param("ii", $id, $mailingid);
  $stmt->execute();
  $stmt->close();
} else {
  die("Error in subscriptions query: " . $conn->error);
}
header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>