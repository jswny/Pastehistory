<!DOCTYPE HTML>

<html>
  <head>

    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-71777353-1', 'auto');
      ga('send', 'pageview');
    </script>

    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index - Paste History</title>
    <link rel="stylesheet" href="/css/common.css" type="text/css"/>
    <link rel="stylesheet" href="/css/markdown.css" type="text/css"/>
    <link rel="stylesheet" href="/css/responsive.css" type="text/css"/>

    <link rel="apple-touch-icon" sizes="57x57" href="/img/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/img/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon-16x16.png">
    <link rel="manifest" href="/img/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

  </head>

  <body class="markdown-body">
    <div id="container">
      <?php
        // require 'bin/init.php';
        //
        // date_default_timezone_set('America/New_York');
        //
        // $sql = 'SELECT MAX( id ) AS max FROM archive';
        // $result = $conn->query($sql);
        // $row = $result->fetch_assoc();
        // $last_id = $row['max'];
      ?>

      <h1 class="center"><a id="header-link" href="/">Paste History</a> <small>Beta</small></h1>

      <!insertHere>

      <?php
        // $sql = 'SELECT * FROM archive';
        // $result = $conn->query($sql);
        // $count = $result->num_rows;
        //
        // $sql = "SELECT * FROM archive WHERE id='".$last_id."'";
        // $result = $conn->query($sql);
        // $row = $result->fetch_assoc();
        // $last_crawl_time = $row['last_crawl'];
        //
        // $last_crawl_time = date('h:i:s A', $last_crawl_time);
      ?>
      <div class="center" id="footer">
        <p>
          <a href="/">Home</a> - <a href="/search.php">Search</a> - <a href="/archive.php">Index</a>
          <br>
          <!-- Total pastes archived: <?php //echo $count; ?> <br>Last crawled: <?php //echo $last_crawl_time; ?> -->
        </p>
      </div>
    </div>
  </body>
</html>
