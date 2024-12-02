<?php
$host = 'localhost';
$username = 'root';
$password = 'root';
$dbname = 'school_db';

$conn= new mysqli($host, $username, $password, $dbname);

// For checking if connection is
// successful or not
if ($conn->connect_error) {
    die("Connection failed: "
        . $conn->connect_error);
}
// echo "Connected successfully";
?>
