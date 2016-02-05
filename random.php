<!DOCTYPE HTML>

<?php
  require 'bin/init.php';

  $sql = 'SELECT MAX( id ) AS max FROM archive';
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $last_id = $row['max'];
?>

<html>
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Experimental - Pastehistory</title>
    <link rel="stylesheet" href="/css/common.css" type="text/css"/>
    <link rel="stylesheet" href="/css/markdown.css" type="text/css"/>
    <link rel="stylesheet" href="/css/responsive.css" type="text/css"/>

    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/img/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
  </head>

  <body class="markdown-body">
    <div id="container">

      <h1 class="center"><a id="header-link" href="/">Pastehistory</a></h1>

      <?php
        $choose = rand(0, 1) == 0;
        if ($choose) {
            $response = file_get_contents('http://pastebin.com/api_scraping.php?limit=101&lang=html5');
        } else {
            $response = file_get_contents('http://pastebin.com/api_scraping.php?limit=101&lang=html4strict');
        }

        $array = json_decode($response, true);

        $rand = rand(1, 100);
        $paste = $array[$rand];

        $pid = $paste['key'];
        $title = $paste['title'];
        $date = $paste['date'];
        $user = $paste['user'];
        $size = $paste['size'];
        $syntax = $paste['syntax'];

        $text = file_get_contents('http://pastebin.com/api_scrape_item.php?i='.$pid);

        if (strlen($title) == 0) {
            $title = 'Untitled';
        }

        $date = date('j F Y h:i:s A', $date);

        echo '<table>';
        echo '<tbody>';
        echo '<tr><td><strong>Paste ID: </strong></td><td><a href="https://pastebin.com/'.$pid.'">'.$pid.'</td></tr>';
        echo '<tr><td><strong>Title: </strong></td><td>'.$title.'</td></tr>';
        echo '<tr><td><strong>Date created: </strong></td><td>'.$date.'</td></tr>';
        echo '<tr><td><strong>User: </strong></td><td>'.$user.'</td></tr>';
        echo '<tr><td><strong>Size: </strong></td><td>'.$size.'</td></tr>';
        echo '<tr><td><strong>Syntax: </strong></td><td>'.$syntax.'</td></tr>';
        echo '</tbody>';
        echo '</table>';
        echo '<pre><textarea>'.htmlentities($text).'</textarea></pre>';

        echo '<div id="output-container">'.$text.'</div>';
      ?>

      <?php
        $sql = 'SELECT * FROM archive';
        $result = $conn->query($sql);
        $count = $result->num_rows;

        $sql = "SELECT * FROM archive WHERE id='".$last_id."'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $last_crawl_time = $row['last_crawl'];

        $last_crawl_time = date('h:i:s A', $last_crawl_time);

        $conn->close();
      ?>
      <div class="center" id="footer">
        <p>
          <a href="/">Home</a> - <a href="/search.php">Search</a> - <a href="/archive.php">Index</a> - <a href="/random.php">Experimental</a>
          <br>
          Total pastes archived: <?php echo $count; ?> <br>Last crawled: <?php echo $last_crawl_time; ?>
        </p>
      </div>
    </div>
  </body>
</html>
