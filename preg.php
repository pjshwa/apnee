
<?php
include("header.php");
echo '<div class="container">';
$preginput = '<h3>정규식 패턴과 그 패턴을 찾을 문자열을 입력</h3><form id="form" method="post"><label for="pattern">정규식 패턴 </label><input type="text" id="pattern" name="pattern"/><br/><label for="str">문자열 </label><input type="text" id="str" name="str"/><br/></form><div onclick="subm();" class="btn btn-primary">제출해 볼까요?</div><script>function subm(){document.forms["form"].submit();}</script>';
if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    echo $preginput;
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
	$matches = array();
	$pattern = '~' .$_POST["pattern"] . '~';
	$string = $_POST["str"];
	preg_match($pattern, $string, $matches, PREG_OFFSET_CAPTURE);
    print_r($matches);
    echo '<br/>만족하셨나요?<br/><a href="preg.php" class="btn btn-success">다시 해보기</a>';
}
?>
</div>

<img src="./static/pics/moomin.png" width="30%"/>


<?php include("footer.php"); ?>