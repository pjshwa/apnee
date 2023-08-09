<?php
  require('db.php');

  $items = $db->getDailyLeetCodeList();

  echo "<h1>릿뚝방 Hard List</h1>";
  echo "<ul>";
  foreach ($items as $item) {
    $url = $item['url']; $date = $item['date'];

    // break if date exceeds today
    if ($date > date("Y-m-d")) break;

    // open in new tab
    echo "<li>$date: <a href='$url' target='_blank'>$url</a></li>";
  }
  echo "</ul>";
?>
