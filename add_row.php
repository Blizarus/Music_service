<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
$conn = new mysqli('music', 'root', '', 'music');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Arrays of user and audio file IDs
$userIds = array(1, 2, 3, 4, 5, 12, 13, 14, 15, 16);
$audioFileIds = array(27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47);

// Start and end dates
$startDate = '2022-12-01';
$endDate = '2023-01-29';

// Generate array of months between start and end dates
$period = new DatePeriod(
    new DateTime($startDate),
    new DateInterval('P1M'),
    new DateTime($endDate . ' 23:59:59')
);

// Loop through each month in the period
foreach ($period as $dt) {
    $startMonth = $dt->format('Y-m-01');
    $endMonth = $dt->format('Y-m-t');

    // Insert 100 records for the current month
    for ($i = 0; $i < 25; $i++) {
        $randomUserId = $userIds[array_rand($userIds)];
        $randomAudioFileId = $audioFileIds[array_rand($audioFileIds)];
        $randomDate = date('Y-m-d', mt_rand(strtotime($startMonth), strtotime($endMonth)));

        // SQL query to insert the record
        $sql = "INSERT INTO statistic (customerid, audiofileid, listeningdate) 
                VALUES ('$randomUserId', '$randomAudioFileId', '$randomDate')";

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

echo "100 records inserted successfully for each month.";

// Close connection
$conn->close();
?>
