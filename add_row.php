<?php
// Replace these values with your actual database credentials
$conn = new mysqli('music', 'root', '', 'music');


// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Arrays of user and audio file IDs
$userIds = array(1, 2, 3, 4, 5, 12, 13, 14, 15, 16);
$audioFileIds = array(27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47);

// Loop to insert 100 records
for ($i = 0; $i < 25; $i++) {
  $randomUserId = $userIds[array_rand($userIds)];
  $randomAudioFileId = $audioFileIds[array_rand($audioFileIds)];
  // $randomDate = date('Y-m-d', mt_rand(strtotime('2020-01-01'), strtotime('2024-05-26')));

  $randomDate = date('Y-m-d', mt_rand(strtotime('2022-12-01'), strtotime('2023-01-31')));

  // SQL query to insert the record
  $sql = "INSERT INTO statistic (customerid, audiofileid, listeningdate) 
          VALUES ('$randomUserId', '$randomAudioFileId', '$randomDate')";

  if ($conn->query($sql) !== TRUE) {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}

echo "100 records inserted successfully";

// Close connection
$conn->close();
?>