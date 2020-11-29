<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    require('db.php');
    $db->newPika($_POST['player_nickname'], $_POST['success'], $_POST['remain_time_score'], $_POST['total_hits_score']);
    header("Location: " . $_SERVER['REQUEST_URI']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="description" content="">
    <meta name="author" content="">

    <title>피카추 잡기</title>

    <!-- Bootstrap Core CSS -->
    <link href="./static/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="./favicon.ico">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery -->
    <script src= "./static/js/jquery.js"></script>
    <script src='./static/js/jquery.imgexplode.js'></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="./static/js/bootstrap.min.js"></script>

</head>
<body>

<form name="score" action="pika.php" method="post">
<input type="hidden" id="player_nickname" name="player_nickname"/>
<input type="hidden" id="success" name="success"/>
<input type="hidden" id="remain_time_score" name="remain_time_score"/>
<input type="hidden" id="total_hits_score" name="total_hits_score"/>
</form>
<div id="start" class="btn btn-large btn-success" style="width:100%; height:80px; display: block; visibility: hidden;" onclick="start();">시작하기</div>
<div id="leaderboard" class="btn btn-large btn-danger" style="width:100%; height:80px; display: block; visibility: hidden;" onclick="leaderboard();">리더보드</div>
<img id="pika" src="./static/images/pika.png" style="visibility: hidden;"/>
<div id="hit_rect_0" class="hit_rect" onclick="deductHealth(70);"></div>
<div id="hit_rect_1" class="hit_rect" onclick="deductHealth(40);"></div>
<div id="hit_rect_2" class="hit_rect" onclick="deductHealth(40);"></div>
<div id="hit_rect_3" class="hit_rect" onclick="deductHealth(40);"></div>
<div id="hit_rect_4" class="hit_rect" onclick="deductHealth(100);"></div>
<!-- style="visibility: hidden;"></div> -->
<div id="remaining_health_2" class="health_bar" style="visibility: hidden;"></div>
<div id="remaining_health" class="health_bar" style="visibility: hidden;"></div>
<div id="total_health" class="health_bar" style="visibility: hidden;"></div>
<div id="timer_text" style="visibility: hidden;"><span style="color:white; display: inline-block; font-size: 500%; text-align: right;" id="remaining_time"></span></div>
<div id="hits_text"><span style="color:white; display: inline-block; font-size: 200%; text-align: left;" id="hits_score">LOADING</span></div>

<style>
html {
    height: 100%;
    background: url(./static/images/stars-min.png) no-repeat center center fixed; 
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
@media only screen and (min-width: 320px) { 
    #pika {
        width: 40%;
        position: absolute;
    } 
} 
@media only screen and (min-width: 769px) { 
    #pika {
        width: 25%;
        position: absolute;
    } 
} 


.hit_rect {
    border-style:none;
    position: absolute;
}

.health_bar {
    width: 100%;
    height: 5%;
    top: 0;
    border-style:none;
    position: absolute;
}

#remaining_health_2 {
    background-color: #cf9;
    z-index: 1;
}

#remaining_health {
    background-color: #ff6;
    z-index: 0;
}

#total_health {
    background-color: #c00;
    z-index: -1;
}

#timer_text {
    position: fixed;
    right: 10px;
    bottom: 0;
}

#hits_text {
    position: fixed;
    left: 10px;
    bottom: 0;
}

</style>
<script>
var time = 60;
var remaining_time_span = document.getElementById("remaining_time");
var hits_score_span = document.getElementById("hits_score");
var pika = document.getElementById("pika"); 
var hit_rects = document.getElementsByClassName("hit_rect");
var health_bars = document.getElementsByClassName("health_bar");
var hits = 0;
var game_over = false;
var max_health = 
<?php
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
if(isMobile()){
    echo "14000";
}
else echo "11000"; ?>;
var health = max_health;
var health_bar = document.getElementById("remaining_health");
var health_bar_2 = document.getElementById("remaining_health_2");
// audios
var bgm = new Audio('./m/battle.ogg');
var hit_01 = new Audio('./m/hit1.ogg');
var hit_02 = new Audio('./m/hit2.ogg');
var hit_03 = new Audio('./m/hit3.ogg');
var explode = new Audio('./m/explode.ogg');
var nothing_audio = new Audio('./m/nothingcanstop.mp3');
bgm.loop = true;
var hit_rects_pos = {
    0:{"top":0.8, "left":0.3, "width":0.4, "height":0.2},
    1:{"top":0.3, "left":0.3, "width":0.4, "height":0.2},
    2:{"top":0.5, "left":0.3, "width":0.4, "height":0.3},
    3:{"top":0.0, "left":0.0, "width":0.3, "height":1.0},
    4:{"top":0.3, "left":0.7, "width":0.3, "height":0.7},
};

var loaded = false;
$(window).on("load", function() {
    loaded = true;
    hits_score_span.innerHTML = "";
    document.getElementById("start").style.visibility = "visible";
    document.getElementById("leaderboard").style.visibility = "visible";
});

function leaderboard(){
    window.location.href = './pika_leaderboard.php';
}

function start(){
    if(loaded){
        movePika();
        pika.style.visibility = "visible";
        document.getElementById("timer_text").style.visibility = "visible";
        remaining_time_span.innerHTML = time;
        for(var i = 0; i < hit_rects.length; i++){
            hit_rects[i].style.visibility = "visible";
        }
        for(var i = 0; i < health_bars.length; i++){
            health_bars[i].style.visibility = "visible";
        }
        bgm.play();
        document.getElementById("start").style.display = "none";
        document.getElementById("leaderboard").style.display = "none";
        var x = setInterval(function() {
          // Display the result in the element with id="demo"
          time--;
          remaining_time_span.innerHTML = time;
          if (time < 20) {
            remaining_time_span.style.color = "red";
          }
          // If the count down is finished, write some text 
          if (time < 0) {
            gameClear(false);
          }
          if(game_over) clearInterval(x);
        }, 1000);
    }
}

var de = document.documentElement.getBoundingClientRect();
var x_limit = de.right - de.left - pika.width;
var y_limit = de.bottom - de.top - pika.height;
var viewlimit = setInterval(deCalc, 500);

function randomIntFromInterval(min,max)
{
    return Math.floor(Math.random()*(max-min+1)+min); // rand number generator
}

var gameClear_busy = false;

function gameClear(result){
    if(!gameClear_busy){
        game_over = true;
        gameClear_busy = true;
        bgm.pause();
        if(result){
            explode.play();
            $("#pika").explode({"minWidth":3,"maxWidth":12,"radius":235,"minRadius":15,"release":true,"fadeTime":1,"recycle":false,"recycleDelay":500,"explodeTime":80,"round":false,"minAngle":0,"maxAngle":360,"gravity":10});
            setTimeout(function(){
                document.getElementById("success").value = 1;
                document.getElementById("remain_time_score").value = time;
                document.getElementById("total_hits_score").value = hits;
                var winner = prompt("축하축하!!! 성공했습니다. 리더보드에 올라갈 이름을 입력하세요:", "");
                document.getElementById("player_nickname").value = winner; 
                document.forms["score"].submit();
            }, 1500);
            
        }
        else{
            document.getElementById("timer_text").style.visibility = "hidden";
            alert('시간 초과!');
            document.getElementById("success").value = 0;
            document.getElementById("remain_time_score").value = 0;
            document.getElementById("total_hits_score").value = hits;
            if(confirm('게임 오버! 그래도 리더보드에 등록할까요?')){
                var player = prompt("등록할 이름을 입력:", "");
                document.getElementById("player_nickname").value = player;
                document.forms["score"].submit();
            }
            else window.location.reload();
        }
    }
}

function hitEffect(){
    hits++; 
    var i = randomIntFromInterval(1,3);
    if(i == 1){
        hit_01.play();
        // pika.src = "./static/images/pika-hit1.png";
    }
    else if(i == 2){
        hit_02.play();
        // pika.src = "./static/images/pika-hit2.png";
    }
    else if(i == 3){
        hit_03.play();
        // pika.src = "./static/images/pika-hit2.png";
    }
}

var nothing_audio_played = false;

function deductHealth(amount){
    console.log('click');
    health -= amount;
    hitEffect();
    if (health < 0) {
        gameClear(true);
    }
    
    var angry = (2 * health < max_health);
    if(!angry) health_bar_2.style.width = Math.floor(((2 * health / max_health) - 1) * (de.right - de.left)) + 'px';
    else{
        if(!nothing_audio_played){
            nothing_audio.play();
            nothing_audio_played = true;
        }
        health_bar_2.style.visibility = "hidden";
        health_bar.style.width = Math.floor((health * 2 / max_health) * (de.right - de.left)) + 'px';
    }
}

function deCalc(){
    de = document.documentElement.getBoundingClientRect();
    x_limit = de.right - de.left - pika.width;
    y_limit = de.bottom - de.top - pika.height;
}

function movePika() {
    var x_pos = 0;
    var y_pos = 0;
    var x_dir = true;
    var y_dir = true;
    var id = setInterval(frame, 30);
    var r = setInterval(randMovement, 400);
    function adjustHitRects(x_pos, y_pos){
        for(var i = 0; i < hit_rects.length; i++){
            hit_rects[i].style.top = Math.floor(y_pos + pika.height * hit_rects_pos[i]["top"]) + 'px';
            hit_rects[i].style.left = Math.floor(x_pos + pika.width * hit_rects_pos[i]["left"]) + 'px';
            hit_rects[i].style.width = Math.floor(pika.width * hit_rects_pos[i]["width"]) + 'px';
            hit_rects[i].style.height = Math.floor(pika.height * hit_rects_pos[i]["height"]) + 'px';
        }
    }
    function randMovement(){
        
        const tolerance = 7;
        const max = 10;

        var x_rand = randomIntFromInterval(0, max);
        var y_rand = randomIntFromInterval(0, max);
        if(x_rand > tolerance){
            x_dir = !x_dir;
        }
        if(y_rand > tolerance){
            y_dir = !y_dir;
        }
    }
    function frame() {
        var angry = (2 * health < max_health);
        if (x_pos > x_limit) {
            x_dir = false;
        } 
        else if (x_pos < de.left){
            x_dir = true;
        }
        if (y_pos < de.top) {
            y_dir = true;
        } 
        else if (y_pos > y_limit){
            y_dir = false;
        }
        var move = 10;
        if (angry) move *= 1.5;
        if(x_dir) x_pos += move;
        else x_pos -= move;
        if(y_dir) y_pos += move;
        else y_pos -= move;
        pika.style.top = y_pos + 'px'; 
        pika.style.left = x_pos + 'px'; 

        hits_score_span.innerHTML = "Hits: " + hits;

        adjustHitRects(x_pos, y_pos);
    }
    
}
</script>
</body>
</html>