<?php
function deleteDirectory($directoryPath) {
  if (!is_dir($directoryPath)) {
      return false;
  }

  $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($directoryPath, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::CHILD_FIRST
  );

  foreach ($iterator as $file) {
      if ($file->isDir()) {
          rmdir($file->getRealPath());
      } else {
          unlink($file->getRealPath());
      }
  }

  rmdir($directoryPath);
  return true;
}

$artist = $_GET['id'];

$conn = new mysqli('music', 'root', '', 'music');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "select * from artist where artistid=".$artist."";
$result = $conn->query($sql);
$composition = $result->fetch_row();

$directoryPath = dirname($composition[2]);

deleteDirectory($directoryPath);
$conn->query("delete from artist where artistid=".$artist."");

header("Location: " . $_SERVER["HTTP_REFERER"]);
exit();
?>