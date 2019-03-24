<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
	header('Content-Type: application/json');
	echo json_encode(array("type"=> "text"));
}
else {
  header('Location: index.php');
}
?>
