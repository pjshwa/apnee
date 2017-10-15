<?php
require('db.php');
if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$db->updateContent($_POST['content_id'], $_POST['content']);
}
?>