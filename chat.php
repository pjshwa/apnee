<?php
include("header.php");
include("credentials.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8"); // 인코딩 박살 방지
    $name = $_POST["chat_name"];
    $content = $_POST["chat_content"];
    $stmt = $conn->prepare("insert into chats (author, content, reg_date) values (?, ?, CURRENT_TIMESTAMP)");
    $stmt->bind_param('ss', $name, $content);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: " . $_SERVER['REQUEST_URI']);
}

?>
<div class="container">
<div class="jumbotron" style="padding:20px 5px 20px 5px;">
<div id="chatty">
<ul class="freeboard">
<?php
    $conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8"); // 인코딩 박살 방지
    $sql = "select author, content from chats order by reg_date desc limit 15"; // limit 30 : recent 30 acts
    $r = $conn->query($sql);
    if ($r->num_rows > 0) {
            // output data of each row

        while($row = $r->fetch_assoc()) {
            echo "<li>".htmlspecialchars($row["author"])." :".htmlspecialchars($row["content"])."</li>";
        }
    } else {
        echo "내용 없음";
    }
    $conn->close();
?>
</ul>
</div>
</div>

<form action="chat.php" method="post">
<?php
$rand = mt_rand(0,3);
$animal = "";
if($rand == 0){
    $animal = "익명의 황소";
}
elseif($rand == 1){
    $animal = "익명의 젖소";
}
elseif($rand == 2){
    $animal = "익명의 코뿔소";
}
else{
    $animal = "익명의 사슴";
}
echo '<input id="id_author" maxlength="30" name="chat_name" value="'.$animal.'" required="required" title="" type="text" />'
?>

<input id="id_chat_content" maxlength="100" name="chat_content" placeholder="내용 입력" required="required" title="" type="text" />
<button class="btn btn-success btn-sm" type="submit">GO!</button>
</form>
<script>
$(function() {
    function callAjax(){
        $('#chatty').load(window.location.pathname+' #chatty');
    }
    setInterval(callAjax, 3000 );
});
</script>
</div>
<?php include("footer.php"); ?>