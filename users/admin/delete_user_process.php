<?php
$customerid = $_GET['id'];

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->query("delete from customers where customerid=".$customerid."");

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>