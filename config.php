<?php
$databaseHost = "localhost";
$databaseName = "habittracker";
$databaseUsername = "root";
$databasePassword = "";

$mysqli = mysqli_connect(
    $databaseHost,
    $databaseUsername,
    $databasePassword,
    $databaseName
);

if (!$mysqli) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
