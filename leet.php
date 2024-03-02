<?php
  require('db.php');

  $now = gmdate('Y-m-d');
  $url = $db->getDailyLeetCode($now);

  if ($url == null) {
    $url = "https://www.leetcode.com";
  }

  // Redirect to the daily leetcode problem
  header("Location: $url");
?>
