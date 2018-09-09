<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $comment_id = $_POST["comment_id"];
    $password = $_POST["password"];

    if($password === ''){
        echo "<script>alert('비밀번호를 입력해 주세요');</script>";
    }
    else {
        if($db->deleteComment($comment_id, $password)){
            echo "<script>alert('삭제됨');</script>";
        }
        else {
            echo "<script>alert('비밀번호가 틀립니다');</script>";
        }
    }
}

header("Location: eggs/index.php");

?>