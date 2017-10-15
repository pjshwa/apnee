<?php
require('db.php');
$db->addCoffee();
header('Location: coffee.php');
?>