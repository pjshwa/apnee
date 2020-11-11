<?php
require('db.php');

if($_SERVER['REQUEST_METHOD'] === 'GET'){
	header('Content-Type: application/json');
	echo json_encode($db->getEventById($_GET['event_id']));
}
?>
