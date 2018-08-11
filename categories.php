<?php
include("header.php");?>
<!-- <script>NekoType="colourful"</script>
<h1 id=nl><script src="http://webneko.net/n200504.js"></script></h1> -->
<img id="apeach" src="./static/pics/apeachdance.gif" style="" onclick="apeachClick();" />
<div class="container" style="margin-top:20px;"><h3>분류</h3>

<div id="front_row" class="row" style="margin-top:40px;">

<div class="col-md-4">
<h5>완성 또는 더 고칠 생각이 없음</h5>
<ul>
	<li>
	    <a href = "./aeogae.php">애오개</a>
	</li>
	<li>
	    <a href = "./snow.php">눈</a>
	</li>
	<li>
	    <a href = "./chat.php">채팅방</a>
	</li>
	<li>
	    <a href = "./preg.php">정규식 테스터</a>
	</li>
	<li>
	    <a href = "./cow.php">얼룩소</a>
	</li>
</ul></div>
<div class="col-md-4">
<h5>미완성</h5>
<ul>
	<li>
	    <a href = "./foretooth">앞니저지</a>
	</li>
	<li>
	    <a href = "./philosophy.php">철학자 채팅</a>
	</li>
	<li>
	    <a href = "./pika.php">피카추 잡기(베타)</a>
	</li>
	<li>
	    <a href = "./aeogae2.php">애오개 2</a>
	</li>
</ul></div>
<div class="col-md-4">
<h5>기타</h5>
<ul>
	<li>
	    <a href = "./coffee.php">커피 일기</a>
	</li>
	<li>
	    <a href = "./collections.php">수집 목록</a>
	</li>
	<li>
	    <a href = "./idea.php">아이디어?</a>
	</li>
	<li>
	    <a href = "./egg"><img src="./static/pics/egg.png" style="width: 10%;"/></a>
	</li>
</ul>

</div>
</div>
<div id="front_row" class="row" style="margin-top:10px;">
<div class="col-md-4"></div>
<div class="col-md-4">
<h5>구상중</h5>
<ul>
	<li>
		앞니 퀴즈 퀴즈쇼
	</li>
	<li>
	    다른 것 잡기
	</li>
</ul>
</div>
<div class="col-md-4">
<h5>방명록~</h5>
<ul>
	<li>
	    <a href = "./visitor.php">방명록</a>
	</li>
</ul>
</div>

</div>
</div>
<style type="text/css">
#apeach {
	display: inline;
	position: absolute;
	left:0;top:0;
}
@media only screen and (min-width: 320px) { 
    #apeach {
        width: 30%;
    } 
} 
@media only screen and (min-width: 769px) { 
    #apeach {
        width: 10%;
    } 
} 
</style>
<script>

var clicks = 0;
<?php
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
if(isMobile()){
    echo "const move = 30;";
}
else echo "const move = 60;"; 
?>
var x_move = randomFromInterval(1, Math.sqrt(move-1));
var y_move = Math.sqrt(move - x_move * x_move);

$(window).on("load", function() {
	moveApeach();
	setbackground();
});

var apeach = document.getElementById("apeach"); 
var move_toggle = false;
var de = document.documentElement.getBoundingClientRect();
var x_limit = de.right - apeach.width;
var y_limit = de.bottom - apeach.height;
var viewlimit = setInterval(deCalc, 500);

function deCalc(){
    de = document.documentElement.getBoundingClientRect();
    x_limit = de.right - 2*de.left - apeach.width;
    y_limit = de.bottom - 2*de.top - apeach.height;
}

function randomFromInterval(min,max)
{
    return Math.random()*(max-min+1); // rand number generator
}

function changeDirection(){
	x_move = randomFromInterval(1, Math.sqrt(move-1));
	y_move = Math.sqrt(move - x_move * x_move);
}

function setbackground()
{
	var ColorValue = '';
    var x = setInterval(function(){
    	if(move_toggle){
    		var index = Math.round(Math.random() * 8);

		    ColorValue = "CCCCCC"; // default color - white (index = 0)
		    if(index == 1)
		        ColorValue = "66FF33"; 
		    if(index == 2)
		        ColorValue = "FF0000"; 
		    if(index == 3)
		        ColorValue = "FF00FF"; 
		    if(index == 4)
		        ColorValue = "0000FF"; 
		    if(index == 5)
		        ColorValue = "00FFFF"; 
		    if(index == 6)
		        ColorValue = "FFFF00"; 
		    if(index == 7)
		       ColorValue = "CC66FF"; 
		    if(index == 8)
		        ColorValue = "3366FF";
		    $("body").css("background", "#"+ColorValue);
    	}
    }, 600); //  milliseconds delay

    
}

var bgm = new Audio('./m/sandcanyon.ogg');

function apeachClick(){
	clicks++;
	changeDirection();
    move_toggle = !move_toggle;
    if(!move_toggle){
    	$("body").css("background", "#FFFFFF");
    	bgm.pause();
    }
    else bgm.play();
}

function moveApeach() {
    var x_pos = 0;
    var y_pos = 0;
    var x_dir = true;
    var y_dir = true;
    var id = setInterval(frame, 30);

    function frame() {
    	if(move_toggle){
    		if (x_pos > x_limit) {
	            x_dir = false;
	        } 
	        else if (x_pos < -de.left){
	            x_dir = true;
	        }
	        if (y_pos < -de.top) {
	            y_dir = true;
	        } 
	        else if (y_pos > y_limit){
	            y_dir = false;
	        }
	        if(x_dir) x_pos += x_move;
	        else x_pos -= x_move;
	        if(y_dir) y_pos += y_move;
	        else y_pos -= y_move;
	        apeach.style.top = y_pos + 'px'; 
	        apeach.style.left = x_pos + 'px';
    	}
    }
}
</script>

<?php include("footer.php"); ?>