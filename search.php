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
    <title>Search - Pastehistory</title>
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

      <form action="" method="POST">
        <p>
          Enter number of pastes to search:
          <input type="text" name="n" id="n"/>
          <strong>or</strong> Search archived?
          <input type="checkbox" name="archived" value="true"/>
        </p>
        <p>
          Enter keywords to search for (comma seperated):
          <input type="text" name="keywords" id="keywords"/>
          <input type="submit" value="Submit"/>
        </p>
      </form>

      <?php
        if (isset($_POST['n'])) {
            $limit = htmlentities($_POST['n']);

            if (preg_replace('/[^a-zA-Z0-9]+/', '', $limit) != $limit) {
                die('Fuck you.');
            }

            if (filter_var($limit, FILTER_SANITIZE_NUMBER_INT) != $limit) {
                die('Fuck you.');
            }

            if ($limit > 500) {
                $limit = 500;
            }

            if ($limit <= 0) {
                $limit = 15;
            }
        } else {
            $limit = 15;
        }

        if (isset($_POST['keywords']) && $_POST['keywords'] != '') {
            $keywords = htmlentities($_POST['keywords']);
            $keywords = str_replace(' ', '', $keywords);

            if (preg_replace('/[^a-zA-Z0-9\,]+/', '', $keywords) != $keywords) {
                die('Fuck you.');
            }

            $keywords_array = explode(',', $keywords);
        } else {
            $keywords = false;
        }

        if (isset($_POST['archived'])) {
            if (!$keywords) {
                echo '<p><strong>Error</strong>: Please enter keywords to search for.</p>';
            } else {
                $checked = [];

                foreach ($keywords_array as $word) {
                    $sql = "SELECT * FROM archive WHERE title LIKE '%".$word."%' OR text LIKE '%".$word."%'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0 && count($checked) == 0) {
                        echo '<table id="archive-table">';
                        echo '<tbody>';
                        echo '<tr><td><strong>ID</strong></td><td><strong>Title</strong></td><td><strong>Date</strong></td><td class="responsive-td"><strong>User</strong></td><td class="responsive-td"><strong>Size</strong></td></tr>';
                    }

                    while ($row = $result->fetch_assoc()) {
                        $pid = $row['pid'];

                        if (!in_array($pid, $checked)) {
                            $title = $row['title'];
                            $date = $row['date'];
                            $user = $row['user'];
                            $size = $row['size'];
                            $syntax = $row['syntax'];
                            $text = $row['text'];
                            $last_crawl = $row['last_crawl'];

                            $checked[] = $pid;

                            $text = str_replace('"', '&quot;', $text);

                            if (strlen($title) == 0) {
                                $title = '<small>Untitled</small>';
                            }

                            $date = date('j/n/Y h:i:s A', $date);
                            $last_crawl = date('j/n/Y h:i:s A', $last_crawl);

                            echo '<tr>'.'<td><a title="'.$text.'"'.'href="/?id='.$pid.'">'.$pid.'</a></td><td>'.$title.'</td><td>'.$date.'</td><td class="responsive-td">'.$user.'</td><td class="responsive-td">'.$size.'</td></tr>';
                        }
                    }
                }

                if (count($checked) == 0) {
                    echo '<p>No results found!</p>';
                } else {
                    echo '</tbody>';
                    echo '</table>';
                }
            }
        } else {
            $response = file_get_contents('http://pastebin.com/api_scraping.php?limit='.$limit);

            $array = json_decode($response, true);

            $count = 0;

            foreach ($array as $item) {
                $pid = $item['key'];
                $text = file_get_contents('http://pastebin.com/api_scrape_item.php?i='.$pid);
                $title = $item['title'];

                if ($keywords != false) {
                    $contains = false;

                    foreach ($keywords_array as $word) {
                        if (stripos($text, $word) !== false || stripos($title, $word) !== false) {
                            $contains = true;
                        }
                    }
                } else {
                    $contains = true;
                }

                if ($contains == true) {
                    if ($count == 0) {
                        echo '<table id="archive-table">';
                        echo '<tbody>';
                        echo '<tr><td><strong>ID</strong></td><td><strong>Title</strong></td><td><strong>Date</strong></td><td class="responsive-td"><strong>User</strong></td><td class="responsive-td"><strong>Size</strong></td></tr>';
                    }

                    ++$count;

                    $date = $item['date'];
                    $user = $item['user'];
                    $size = $item['size'];
                    $syntax = $item['syntax'];
                    $last_crawl = time();

                    $text = str_replace('"', '&quot;', $text);

                    if (strlen($title) == 0) {
                        $title = '<small>Untitled</small>';
                    }

                    $date = date('j/n/Y h:i:s A', $date);
                    $last_crawl = date('j/n/Y h:i:s A', $last_crawl);

                    echo '<tr>'.'<td><a title="'.$text.'"'.'href="https://pastebin.com/'.$pid.'">'.$pid.'</a></td><td>'.$title.'</td><td>'.$date.'</td><td class="responsive-td">'.$user.'</td><td class="responsive-td">'.$size.'</td></tr>';
                }
            }

            if ($count == 0) {
                echo '<p>No results found!</p>';
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
