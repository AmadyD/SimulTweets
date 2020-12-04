<?php

$serverName = "localhost";
$userName = "root";
$password = "";
$database="simultwitter";

$conn = mysqli_connect($serverName,$userName,$password,$database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
//   echo "Connected successfully";
?>
