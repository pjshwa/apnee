<?php
include("header.php");
include("credentials.php");
?><div class="container" style="margin-top:20px;"><h1>지금까지의 검색어</h1><?php

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
		    // output data of each row

		    while($row = $r->fetch_assoc()) {
		        echo "<li>".$row["a_query"]." (".$row["reg_date"].")</li>";
		    }
		} else {
		    echo "검색 결과가 없다";
		}
		echo "</ul>";
	}
	// elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
	// 	$sql = "truncate table queries"; // limit 30 : recent 30 acts
	// 	$r = $conn->query($sql);
	// 	echo "<script>window.alert('다 지웠다');var loc = window.location;window.location = loc.protocol + '//' + loc.host + loc.pathname + loc.search;</script>";
	// }

	$conn->close();

	?>

</div>

<?php include("footer.php"); ?>