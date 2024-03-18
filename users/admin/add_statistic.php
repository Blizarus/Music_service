<?php
session_start();

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$datelisening = date("Y-m-d");
$customerid = $_SESSION['id'];
$compositionid = $_POST['compositionId'];

$stmt = $conn->prepare("INSERT INTO statistic  VALUES (NULL, ?, ?, ?)");
if ($stmt) {
  $stmt->bind_param("iis", $customerid, $compositionid, $datelisening);
  $stmt->execute();
  $stmt->close();
} else {
  die("Error in audiofiles query: " . $conn->error);
}

$conn->close();
?>
