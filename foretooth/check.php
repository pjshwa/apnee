<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
$item = $db->getanswer($_POST['q_key']);
$page = filter_var($_POST['q_page'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]) ?: 1;

if ($item === $_POST['ans']){
	echo "<script>alert('맞았다');";
	$db->updateTries($_POST['q_key'], True);
}
else {
	echo "<script>alert('틀렸다');";
	$db->updateTries($_POST['q_key'], False);
}
echo "location.href='catalog.php?page=".$page."';</script>";
}
else {
	header("Location: catalog.php");
}
?>
