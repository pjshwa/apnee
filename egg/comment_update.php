<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $comment_id = $_POST["comment_id"];
    $author = $_POST["author"];
    $comment = $_POST["comment"];
    $password = $_POST["password"];

    if($password === ''){
        echo "<script>alert('비밀번호를 입력해 주세요');</script>";
    }
    else {
        if($db->updateComment($comment_id, $author, $comment, $password)){
            echo "<script>alert('수정됨');</script>";
        }
        else {
            echo "<script>alert('비밀번호가 틀립니다');</script>";
        }
    }
}

header("Location: egg/index.php");

?>