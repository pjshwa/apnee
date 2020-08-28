<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $article_id = $_POST["article_id"];
    $author = $_POST["comment_author"];
    $comment = $_POST["comment"];
    $db->newComment($article_id, $author, $comment);
}
?>
