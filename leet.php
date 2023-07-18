<?php
  require('db.php');

  $now = gmdate('Y-m-d');
  $url = $db->getDailyLeetCode($now);

  // Redirect to the daily leetcode problem
  header("Location: $url");
?>
