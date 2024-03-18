<?php
$mailingidid = $_GET['id'];

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$conn->query("delete from mailing where mailingid=".$mailingidid);

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>