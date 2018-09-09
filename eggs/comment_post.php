<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $article_id = $_POST["article_id"];
    $author = $_POST["comment_author"];
    $comment = $_POST["comment"];
    $password = $_POST["comment_password"];
    $year = $_POST["cyear"];
    $month = $_POST["cmonth"];

    $db->postComment($article_id, $author, $comment, $password);
}

$params = "year=".$year."&month=".$month."#article_".$article_id;
header("Location: index.php?$params");

?>