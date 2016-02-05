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
    <title>Index - Pastehistory</title>
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
        echo '<table id="archive-table">';
        echo '<tbody>';

        $count = 0;

        for ($i = 0; $i < $last_id; ++$i) {
            $sql = "SELECT * FROM archive WHERE id='".($last_id - $i)."'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $pid = $row['pid'];
            $title = $row['title'];
            $date = $row['date'];
            $user = $row['user'];
            $size = $row['size'];
            $syntax = $row['syntax'];
            $text = $row['text'];
            $last_crawl = $row['last_crawl'];

            $text = str_replace('"', '&quot;', $text);

            if (strlen($title) == 0) {
                $title = '<small>Untitled</small>';
            }

            $date = date('j/n/Y h:i:s A', $date);
            $last_crawl = date('j/n/Y h:i:s A', $last_crawl);

            if ($count == 0) {
                echo '<tr>';
            }

            echo '<td><a href="/?id='.$pid.'">'.$pid.'</a></td>';

            if ($count == 3) {
                echo '</tr>';
                $count = 0;
            } else {
                ++$count;
            }
        }
        echo '</tbody>';
        echo '</table>';
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
