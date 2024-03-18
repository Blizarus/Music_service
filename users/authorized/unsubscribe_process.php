<?php
session_start();
header('Content-Type: text/html; charset=utf-8');

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$id = $_SESSION['id'];

$mailingid = $_GET['id'];

$conn->query("delete from subscriptions where mailingid=".$mailingid." and customerid=".$id);

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>