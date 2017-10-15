<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
$item = $db->getanswer($_POST['q_key']);

if ($item === $_POST['ans']){
	echo "<script>alert('맞았다');";
	$db->updateTries($_POST['q_key'], True);
}
else {
	echo "<script>alert('틀렸다');";
	$db->updateTries($_POST['q_key'], False);
}
echo "location.href='catalog.php?page=".$_POST['q_page']."';</script>";
}
else {
	header("Location: catalog.php");
}
?>
