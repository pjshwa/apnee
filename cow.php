
<?php
include("header.php");
include("credentials.php");
echo '<div class="container">';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8"); // 인코딩 박살 방지

    $score = (int)$_POST["score"];
    if ($score > 50 || $score < 0) {
        $score = -1;
    }

    $stmt = $conn->prepare("insert into cowgame_score (score, reg_date) values (?, CURRENT_TIMESTAMP)");
    $stmt->bind_param('s', $score);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    if ($score == -1) {
        echo "<script>alert('사기꾼! 당신의 점수는 -1점이 되었다.');</script>";
    }
    echo "<script>var loc = window.location;window.location = loc.protocol + '//' + loc.host + loc.pathname + loc.search;</script>"; //force get
}

?>
<style type="text/css">
.cowimg {
    display: inline-block;
    margin: 0;
}
@media only screen and (min-width: 320px) {
    .cowimg {
        width: 48%;
    }
}
@media only screen and (min-width: 769px) {
    .cowimg {
        width: 23%;
    }
}
</style>

<h1>얼룩소 고르는 게임</h1>
<div><span id="score">0</span>점</div>
<div><span id="count">0</span>초</div>
<form id="score" method="post"><input type="hidden" id="sc" name="score"/></form>
<div id="front_row" class="row" style="margin-top:40px;">
<div id="front_image" class="col-md-8">
<img id="intro" class="img-responsive" src="./static/pics/cows/intro.png"/>
<div id="startClock" style="display: inline-block;" class="btn btn-success">시작하기</div>
<div id="sound_box" style="display: inline-block;"><input type="checkbox" name="sound"> 효과음</div>
</div>
<div id="scoreboard" class="col-md-4">
<?php
    $conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8"); // 인코딩 박살 방지
    $sql = "select score, reg_date from cowgame_score where score = (select max(score) from cowgame_score) limit 1";
    $r = $conn->query($sql);
    if ($r->num_rows > 0) {
        // output data of each row
        while($row = $r->fetch_assoc()) {
            echo '<h4 style="color:blue;">최고점수: '.$row["score"]."점 (".$row["reg_date"].")</h4>";
        }
    }
    $sql = "select score, reg_date from cowgame_score order by reg_date desc limit 15"; // limit 30 : recent 30 acts
    $r = $conn->query($sql);
    if ($r->num_rows > 0) {
            // output data of each row
        while($row = $r->fetch_assoc()) {
            echo "<li>".$row["score"]."점 (".$row["reg_date"].")</li>";
        }
    } else {
        echo "점수 없음";
    }
    $conn->close();
?>
</div>
</div>
<div id="ig" style="visibility: hidden;">
<div class="row">
<div id="cowgame">
<header><h2><span id="prompt_span">
</span></h2></header>
<div class="cowimg"><img id="cow_1" class="img-responsive" onclick="javascript:void(0);" src="."/></div>
<div class="cowimg"><img id="cow_2" class="img-responsive" onclick="javascript:void(0);" src="."/></div>
<div class="cowimg"><img id="cow_3" class="img-responsive" onclick="javascript:void(0);" src="."/></div>
<div class="cowimg"><img id="cow_4" class="img-responsive" onclick="javascript:void(0);" src="."/></div>
</div>
</div>
</div>
</div>
<script src= "./static/js/cow.js"></script>
<?php include("footer.php"); ?>
