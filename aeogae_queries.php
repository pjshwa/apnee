<?php
require("header.php");
require("credentials.php");
require("consts/consts.php");
?>
<div class="container" style="margin-top:20px;">
  <h1>지금까지의 검색어</h1>
  <?php

  $conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  } 
  $conn->set_charset("utf8"); // 인코딩 박살 방지
  // sql to create table
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $sql = "select a_query, reg_date from queries order by reg_date desc limit 15"; // limit 30 : recent 30 acts
      $r = $conn->query($sql);
    echo "<ul>";
    if ($r->num_rows > 0) {
      while ($row = $r->fetch_assoc()) {
        $date = new DateTime($row["reg_date"], new DateTimeZone('UTC'));
        $date->setTimezone($TIMEZONE);
        echo "<li>".$row["a_query"]." (".$date->format('Y-m-d H:i:s').")</li>";
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
