<?php
$servername = "127.0.0.1"; // host sql
$username = "root"; // użytkownik sql
$password = ""; // haslo do sql
$dbname = "exambase"; // nazwa bazy danych
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>