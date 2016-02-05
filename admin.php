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
    <title>Admin - Pastehistory</title>
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

      <form action="" method="post">
        <p>
          Enter Paste ID remove/restore:
          <input type="text" name="id" id="id"/>
          <input type="submit" value="Submit"/>
        </p>
        <p>
          Enter Report ID of report to close/re-open:
          <input type="text" name="report-id" id="report-id"/>
          <input type="submit" value="Submit"/>
        </p>
      </form>

      <?php
        if (isset($_POST['id']) && $_POST['id'] != '') {
            $pid = htmlentities($_POST['id']);

            if (preg_replace('/[^a-zA-Z0-9]+/', '', $pid) != $pid) {
                die('Fuck you.');
            }

            $sql = "SELECT * FROM archive WHERE pid='".$pid."'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $remove = $row['remove'];

                if ($remove == 0) {
                    $remove = 1;
                    $message = '<p>Paste '.$pid.' sucessfully removed!</p>';
                } else {
                    $remove = 0;
                    $message = '<p>Paste '.$pid.' sucessfully restored!</p>';
                }

                $sql = 'UPDATE archive SET remove="'.$remove.'" WHERE pid="'.$pid.'"';

                if ($conn->query($sql) === true) {
                    echo $message;
                } else {
                    echo '<p>Error: '.$sql.$conn->error.'</p>';
                }
            } else {
                echo('<p>Paste not yet archived!</p>');
            }
        }

        if (isset($_POST['report-id']) && $_POST['report-id'] != '') {
            $report_id = htmlentities($_POST['report-id']);

            if (preg_replace('/[^a-zA-Z0-9]+/', '', $report_id) != $report_id) {
                die('Fuck you.');
            }

            $sql = "SELECT * FROM report WHERE id='".$report_id."'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $pid = $row['pid'];
                $close = $row['close'];

                if ($close == 0) {
                    $close = 1;
                    $message = '<p>Report '.$report_id.' on paste '.$pid.' sucessfully closed!</p>';
                } else {
                    $close = 0;
                    $message = '<p>Report '.$report_id.' on paste '.$pid.' sucessfully re-opened!</p>';
                }

                $sql = 'UPDATE report SET close="'.$close.'" WHERE id="'.$report_id.'"';

                if ($conn->query($sql) === true) {
                    echo $message;
                } else {
                    echo '<p>Error: '.$sql.$conn->error.'</p>';
                }
            } else {
                echo('<p>Report does not exist!</p>');
            }
        }

        $sql = "SELECT * FROM report WHERE close='".'0'."'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<table>';
            echo '<tbody>';
            echo '<tr><td><strong>Report ID</strong></td><td><strong>ID</strong></td><td><strong>Date</strong></td><td><strong>IP</strong></td><td><strong>Name</strong></td><td><strong>Email</strong></td><td><strong>Reason</strong></td></tr>';

            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $pid = $row['pid'];
                $date = $row['date'];
                $ip = $row['ip'];
                $name = $row['name'];
                $email = $row['email'];
                $reason = $row['reason'];
                $close = $row['close'];

                $date = date('n/j/Y h:i:s A', $date);

                echo '<tr><td>'.$id.'</td><td><a href="/?id='.$pid.'">'.$pid.'</a></td><td>'.$date.'</td><td>'.$ip.'</td><td>'.$name.'</td><td>'.$email.'</td><td>'.$reason.'</td></tr>';
            }

            echo '</table>';
            echo '</tbody>';
        } else {
            echo '<p>No open reports!</p>';
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
