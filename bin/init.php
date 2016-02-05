<?php
  $servername = "localhost";
  $username = "root";
  $password = "password";
  $db = "paste";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $db);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  date_default_timezone_set('America/New_York');
?>
