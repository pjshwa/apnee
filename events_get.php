<?php
require('db.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	header('Content-Type: application/json');
	echo json_encode($db->getEventById($_POST['event_id']));
}
?>