<?php
include('credentials.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8"); // 인코딩 박살 방지

    $poll_date = $_POST["poll_date"];

    $response = $_POST["response"];

    if($response === ''){
        echo "<h1>내용을 입력해주세요</h1>";
        echo "<br/><button onclick='location.href=document.referrer;'>네</button>";
        $conn->close();
    }
    else {
        if(!preg_match('~^[0-9]{8}$~', $poll_date)){
            die($poll_date.': wrong poll date format');
        }

        if(!($stmt = $conn->prepare("insert into poll_".$poll_date." (response) values (?)"))) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }

        $stmt->bind_param('s', $response);

        if(!$stmt->execute()) {
            throw new Exception('DB Error: '.$this->mysqli->error);
        }
        $stmt->close();
        $conn->close();
        echo "<h1>네</h1>";
        echo "<br/><button onclick='location.href=document.referrer;'>돌아가기</button>";

    } 
}

?>