<?php

  function parse() {

    $response = file_get_contents('http://pastebin.com/api_scraping.php?limit=100');

    $array = json_decode($response, true);

    foreach($array as $item) {
      $pid = $item['key'];
      $title = $item['title'];
      $date = $item['date'];
      $user = $item['user'];
      $size = $item['size'];
      $syntax = $item['syntax'];
      $last_crawl = time();
      save($pid, $title, $date, $user, $size, $syntax, $last_crawl);
    }

  }

  function save($pid, $title, $date, $user, $size, $syntax, $last_crawl) {

    $servername = "localhost";
    $username = "root";
    $password = "password";
    $db = "paste";

    $conn = new mysqli($servername, $username, $password, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $check = "SELECT * FROM archive WHERE pid='" . $pid . "'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo 'Paste ID ' . $pid . " already archived!\n";
    } else {

      $text = file_get_contents('http://pastebin.com/api_scrape_item.php?i=' . $pid);

      $keywords = array('paste', 'test');

      foreach($keywords as $keyword) {
        if (stripos($text, $keyword) !== false) {
          $text = $conn->real_escape_string($text);
          $title = $conn->real_escape_string($title);
          $user = $conn->real_escape_string($user);
          $syntax = $conn->real_escape_string($syntax);

          $sql = "INSERT INTO archive (pid, title, date, user, size, syntax, text, last_crawl) VALUES ('" . $pid . "', '" . $title . "', '" . $date . "', '" . $user . "', '" . $size . "', '" . $syntax . "', '" . $text . "', '" . $last_crawl . "')";

          if ($conn->query($sql) === TRUE) {
          } else {
              echo "Error: " . $sql . $conn->error;
          }

          $conn->close();

          echo 'Paste ID ' . $pid . " archived successfully.\n";
        } else {
          echo 'Paste ID ' . $pid . " doesn't contain any of the keywords, skipping.\n";
        }
      }
  }

  date_default_timezone_set('America/New_York');

  echo "\n######## Starting new parse at time: " . date('j F Y h:i:s A') . " ########\n";

  parse();

  echo "\n######## Parse complete at time: " . date('j F Y h:i:s A') . " ########\n";
?>
