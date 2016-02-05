<?php
  date_default_timezone_set('America/New_York');

  echo "\n######## Starting page generation at time: " . date('j F Y h:i:s A') . " ########\n\n";

  require '/var/www/pastehistory.com/public_html/bin/init.php';

  date_default_timezone_set('America/New_York');

  $sql = 'SELECT MAX( id ) AS max FROM archive';
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $last_id = $row['max'];

  $content = '';

  $content .= '<table id="archive-table">';
  $content .= '<tbody>';

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

      echo "Loaded entry number " . $i . " of " . $last_id . "\n";

      $text = str_replace('"', '&quot;', $text);

      if (strlen($title) == 0) {
          $title = '<small>Untitled</small>';
      }

      $date = date('j/n/Y h:i:s A', $date);
      $last_crawl = date('j/n/Y h:i:s A', $last_crawl);

      if ($count == 0) {
        $content .= '<tr>';
      }

      $content .= '<td><a href="/?id='.$pid.'">'.$pid.'</a></td>';

      if ($count == 3) {
        $content .=  '</tr>';
        $count = 0;
      } else {
        $count++;
      }
  }
  $content .= '</tbody>';
  $content .= '</table>';

  $page = file_get_contents('template.php');
  $page = str_replace('<!insertHere>', $content, $page);

  $file = fopen('/var/www/pastehistory.com/public_html/archive.php', 'w+') or die('Unable to open temporary content file!');
  fwrite($file, $page);
  fclose($file);

  echo "\n######## Page generation complete at time: " . date('j F Y h:i:s A') . " ########\n";

?>
