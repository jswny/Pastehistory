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
    <title>Report - Pastehistory</title>
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
      <?php
        require 'bin/init.php';

        $sql = 'SELECT MAX( id ) AS max FROM archive';
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $last_id = $row['max'];
      ?>

      <h1 class="center"><a id="header-link" href="/">Pastehistory</a></h1>

      <?php
        if (isset($_POST['id']) && $_POST['id'] != '') {
            $pid = htmlentities($_POST['id']);

            if (isset($_POST['submit-report'])) {
                $name = htmlentities($_POST['name']);
                $reason = htmlentities($_POST['reason']);
                $email = htmlentities($_POST['email']);

                if (preg_replace('/[^a-zA-Z0-9]+/', '', $pid) != $pid) {
                    die('Fuck you.');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo '<p>Please enter a valid email</p>';
                    echo '<form method="post" action="report.php"><p><input type="hidden" name="id" value="'.$pid.'"><input type="submit" value="Back"></p></form>';
                } else {
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

                        if (strlen($title) == 0) {
                            $title = 'Untitled';
                        }

                        $date = date('j F Y h:i:s A', $date);
                        $last_crawl = date('j F Y h:i:s A', $last_crawl);
                        $report_date = date('j F Y h:i:s A');
                        $report_date_raw = time();

                        $text = htmlentities($text);

                        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
                        }

                        $client_ip = $_SERVER['REMOTE_ADDR'];

                        $to = 'report@pastehistory.com';
                        $subject = '[REPORT] ID: '.$pid;

                        $message = '<h1>Paste report</h1>';
                        $message .= '<table>';
                        $message .= '<tbody>';
                        $message .= '<tr><td><strong>Paste ID: </strong></td><td><a href="https://pastehistory.com/?id='.$pid.'">'.$pid.'</td></tr>';
                        $message .= '<tr><td><strong>Title: </strong></td><td>'.$title.'</td></tr>';
                        $message .= '<tr><td><strong>Date created: </strong></td><td>'.$date.'</td></tr>';
                        $message .= '<tr><td><strong>User: </strong></td><td>'.$user.'</td></tr>';
                        $message .= '<tr><td><strong>Size: </strong></td><td>'.$size.'</td></tr>';
                        $message .= '<tr><td><strong>Syntax: </strong></td><td>'.$syntax.'</td></tr>';
                        $message .= '<tr><td><strong>Archived on: </strong></td><td>'.$last_crawl.'</td></tr>';
                        $message .= '<tr><td><strong>Reported on: </strong></td><td>'.$report_date.'</td></tr>';
                        $message .= '<tr><td><strong>Reporting IP: </strong></td><td>'.$client_ip.'</td></tr>';
                        $message .= '<tr><td><strong>Name: </strong></td><td>'.$name.'</td></tr>';
                        $message .= '<tr><td><strong>Email: </strong></td><td>'.$email.'</td></tr>';
                        $message .= '<tr><td><strong>Reason: </strong></td><td>'.$reason.'</td></tr>';
                        $message .= '</tbody>';
                        $message .= '</table>';
                        $message .= '<br>';
                        $message .= '<pre>'.$text.'</pre>';

                        $header = "From:mailer@pastehistory.com \r\n";
                        $header .= "MIME-Version: 1.0\r\n";
                        $header .= "Content-type: text/html\r\n";

                        $retval = mail($to, $subject, $message, $header);

                        if ($retval) {
                            echo '<p>Paste successfully reported!</p>';
                        } else {
                            echo '</p>Report failed!</p>';
                        }

                        $name = $conn->real_escape_string($name);
                        $email = $conn->real_escape_string($email);
                        $reason = $conn->real_escape_string($reason);

                        $sql = "INSERT INTO report (pid, date, ip, name, email, reason, close) VALUES ('".$pid."', '".$report_date_raw."', '".$client_ip."', '".$name."', '".$email."', '".$reason."', '". 0 ."')";
                        $result = $conn->query($sql);
                        echo '<form method="get" action="/"><p><input type="hidden" name="id" value="'.$pid.'"><input type="submit" value="Back"></p></form>';
                    } else {
                        echo '<p>Paste cannot be reported!</p>';
                        echo '<form method="get" action="/"><p><input type="hidden" name="id" value="'.$pid.'"><input type="submit" value="Back"></p></form>';
                    }
                }
            } else {
                echo '<form method="post" action="report.php">';
                echo '<input type="hidden" name="id" value="'.$pid.'">';
                echo '<p>Your name: <input type="text" name="name" id="name"/></p>';
                echo '<p>Your email: <input type="text" name="email" id="email"/></p>';
                echo '<p>Please include a reason for your report</p>';
                echo '<pre><textarea name="reason"></textarea></pre>';
                echo 'Please ensure the following before submitting your report otherwise it will be <strong>ignored</strong>:<ul><li>You have provided your valid legal name</li><li>You have provided your correct email address</li><li>The information you have submitted is true and correct</li><li>The paste you are reporting pertains to you personally</li><li>You are not accessing Pastehistory from a VPN or proxy of any kind</li></ul>';
                echo '<input type="submit" name="submit-report" value="Submit"></form>';
                echo '<form method="get" action="/"><p><input type="hidden" name="id" value="'.$pid.'"><input type="submit" value="Back"></p></form>';
            }
        } else {
            echo '<p>No paste ID given!</p>';
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
