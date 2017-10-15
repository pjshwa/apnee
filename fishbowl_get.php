<?php
require('db.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	header('Content-Type: application/json');
	echo json_encode($db->getLogs($_POST['start']));
}
else {
	header('Location: index.php');
}
?>