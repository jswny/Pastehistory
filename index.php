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

    <?php
      if (isset($_GET['id']) && $_GET['id'] != '') {
          $pid = htmlentities($_GET['id']);

          if (preg_replace('/[^a-zA-Z0-9]+/', '', $pid) != $pid) {
              die('Fuck you.');
          }

          $sql = "SELECT * FROM archive WHERE pid='".$pid."'";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              $row = $result->fetch_assoc();
              $pid = $row['pid'];
              $title = $row['title'];
              $date = $row['date'];
              $user = $row['user'];
              $size = $row['size'];
              $syntax = $row['syntax'];
              $text = $row['text'];
              $last_crawl = $row['last_crawl'];
              $remove = $row['remove'];

              if ($remove == 1) {
                  echo '<title>Paste Removed - Pastehistory</title>';
              } else {
                  if ($syntax == 'text') {
                      $prefix = '';
                  } else {
                      $prefix = '['.$syntax.'] ';
                  }

                  if (strlen($title) == 0) {
                      echo '<title>'.$prefix.substr($text, 0, 50).' - Pastehistory</title>';
                  } else {
                      echo '<title>'.$prefix.$title.' - Pastehistory</title>';
                  }
              }
          } else {
              echo '<title>Not Archived - Pastehistory</title>';
          }
      } else {
          echo '<title>Home - Pastehistory</title>';
      }
    ?>

    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        include 'bin/ads.php';
      ?>

      <form action="" method="get">
        <p>
          Enter Paste ID to view:
          <input type="text" name="id" id="id"/>
          <input type="submit" value="Submit"/>
        </p>
      </form>

      <?php
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $pid = htmlentities($_GET['id']);

            if ($result->num_rows > 0) {
                if ($remove == 1) {
                    echo '<p>This paste has been removed!</p>';
                } else {
                    if (strlen($title) == 0) {
                        $title = 'Untitled';
                    }

                    $date = date('j F Y h:i:s A', $date);
                    $last_crawl = date('j F Y h:i:s A', $last_crawl);

                    $text = htmlentities($text);

                    echo '<form action="report.php" method="post"><p class="right-justify"><input type="hidden" name="id" value="'.$pid.'"><input type="submit" value="Report"></p></form>';
                    echo '<table>';
                    echo '<tbody>';
                    echo '<tr><td><strong>Paste ID: </strong></td><td><a href="https://pastebin.com/'.$pid.'">'.$pid.'</td></tr>';
                    echo '<tr><td><strong>Title: </strong></td><td>'.$title.'</td></tr>';
                    echo '<tr><td><strong>Date created: </strong></td><td>'.$date.'</td></tr>';
                    echo '<tr><td><strong>User: </strong></td><td>'.$user.'</td></tr>';
                    echo '<tr><td><strong>Size: </strong></td><td>'.$size.'</td></tr>';
                    echo '<tr><td><strong>Syntax: </strong></td><td>'.$syntax.'</td></tr>';
                    echo '<tr><td><strong>Archived on: </strong></td><td>'.$last_crawl.'</td></tr>';
                    echo '</tbody>';
                    echo '</table>';
                    echo '<pre><textarea readonly>'.$text.'</textarea></pre>';
                }
            } else {
                echo '<p>Paste not yet archived!</p>';
            }
        } else {
            echo '<table id="archive-table">';
            echo '<tbody>';
            echo '<tr><td><strong>ID</strong></td><td><strong>Title</strong></td><td><strong>Date</strong></td><td class="responsive-td"><strong>User</strong></td><td class="responsive-td"><strong>Size</strong></td></tr>';
            for ($i = 0; $i <= 15; ++$i) {
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

                $date = date('n/j/Y h:i:s A', $date);

                echo '<tr>'.'<td><a title="'.$text.'"'.'href="?id='.$pid.'">'.$pid.'</a></td><td>'.$title.'</td><td>'.$date.'</td><td class="responsive-td">'.$user.'</td><td class="responsive-td">'.$size.'</td></tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
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
