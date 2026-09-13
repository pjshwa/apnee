<?php
require("header.php");
require_once __DIR__ . '/lib/database.php';
require("consts/consts.php");
?>
<div class="container" style="margin-top:20px;">
  <h1>지금까지의 검색어</h1>
  <?php

  $conn = connectDatabase();
  // sql to create table
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $sql = "select a_query, reg_date from queries order by reg_date desc limit 15"; // limit 30 : recent 30 acts
      $r = $conn->query($sql);
    echo "<ul>";
    if ($r->num_rows > 0) {
      while ($row = $r->fetch_assoc()) {
        $date = new DateTime($row["reg_date"], new DateTimeZone('UTC'));
        $date->setTimezone($TIMEZONE);
        echo "<li>".htmlspecialchars($row["a_query"], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')." (".$date->format('Y-m-d H:i:s').")</li>";
      }
    } else {
        echo "검색 결과가 없다";
    }
    echo "</ul>";
    
  }

  $conn->close();

?>

</div>

<?php require("footer.php"); ?>
