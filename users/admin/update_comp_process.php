<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {


  $name_composition = $_POST['name_composition'];

  $compositionid = $composition[6];

  $artist = $_POST['artist'];
  $artist = explode('|', $artist);
  $artist = str_replace(' ', '_', $artist);
  $artistid = $artist[0];
  $artist = $artist[1];

  $genre = $_POST['genre'];

  $stmt = $conn->prepare("UPDATE composition SET artistid = ?, genreid = ?, name = ? WHERE compositionid = ?");
  if ($stmt) {
    $stmt->bind_param("issi", $artistid, $genre, $name_composition, $compositionid);
    $stmt->execute();
    $stmt->close();
  } else {
    die ("Error in composition query: " . $conn->error);
  }

  $presencevoice = $_POST['presencevoice'];

  $tonality = $_POST['tonality'];

  $BPM = $_POST['BPM'];

  $duration_seconds = $composition[9];

  $coverpath = "C:\\Games\\xampp\\htdocs\\music\\wwwmedia\\composition\\" . $artist . "\\" . $name_composition . ".png";

  if (!empty ($_FILES['music_file']['tmp_name'])) {
    $dateupload = date("Y-m-d");

    $targetFile = "C:\\Games\\xampp\\htdocs\\music\\wwwmedia\\composition\\" . $artist . "\\" . $artist . "-" . str_replace(' ', '_', $name_composition) . ".mp3";
    move_uploaded_file($_FILES['music_file']['tmp_name'], $targetFile);

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/libraries/getID3-1.9.23/getid3/getid3.php');

    $getID3 = new getID3;
    $fileinfo = $getID3->analyze($targetFile);

    $duration_seconds = round($fileinfo['playtime_seconds']);


    if (empty ($_FILES['cover_file']['tmp_name'])) {
      if (isset ($fileinfo['comments']['picture'][0]['data'])) {
        $coverData = $fileinfo['comments']['picture'][0]['data'];
      }
      $image = imagecreatefromstring($coverData);
      if ($image !== false) {
        file_put_contents($coverpath, $coverData);
      }
    }
    $stmt = $conn->prepare("UPDATE audiofiles SET size = ?, filepath = ?, dateupload = ?, coverpath = ? WHERE audiofileid = ?");
    if ($stmt) {
      $stmt->bind_param("dsssi", $filesize, $targetFile, $dateupload, $coverpath, $compositionid);
      $filesize = round(filesize($targetFile) / 1024);
      $stmt->execute();
      $stmt->close();
    } else {
      die ("Error in audiofiles query: " . $conn->error);
    }
  }
  if (!empty ($_FILES['cover_file']['tmp_name'])) {
    $coverFile = $_FILES['cover_file'];
    move_uploaded_file($coverFile['tmp_name'], $coverpath);
  }
  $stmt = $conn->prepare("UPDATE сharacteristics_music SET tonality = ?, BPM = ?, duration = ?, presencevoice = ? WHERE audiofileid = ?");
  if ($stmt) {
    $stmt->bind_param("iidii", $tonality, $BPM, $duration_seconds, $presencevoice, $compositionid);
    $stmt->execute();
    $stmt->close();
  } else {
    die ("Error in сharacteristics_music query: " . $conn->error);
  }
}
?>