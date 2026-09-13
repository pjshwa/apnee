<?php
require_once __DIR__ . '/lib/view_helpers.php';
require("header.php");
require_once __DIR__ . '/lib/database.php';
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
        echo "<li>".escapeHtml($row["a_query"])." (".formatSeoulTime($row['reg_date'], 'Y-m-d H:i:s').")</li>";
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
