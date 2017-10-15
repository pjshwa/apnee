
<?php
include("header.php");
include("credentials.php");
?><div class="container" style="margin-top:20px;"><?php

$greenish = "['rgb(21,201,161)', 'rgb(21,201,161)', 'rgb(1,92,76)', 'rgb(17,117,99)', 'rgb(14,142,113)', 'rgb(23,175,148)', 'rgb(21,201,161)', 'rgb(21,201,161)']";

$input_station = '<h1>기다리세요</h1><form id="form" method="post"><input type="hidden" id="station_1" name="station_name"/></form><script>var station = prompt("역명을 입력해 보시죠", "애오개");document.getElementById("station_1").value = station;document.forms["form"].submit();</script>';
$subwayid = array("1001"=> "1호선", "1002"=> "2호선", "1003"=> "3호선", "1004"=> "4호선", "1005"=> "5호선", "1006"=> "6호선", "1007"=> "7호선", "1008"=> "8호선", "1009"=> "9호선", "1063"=> "경의중앙선", "1065"=> "공항철도", "1067"=> "경춘선", "1071"=> "수인선", "1075"=> "분당선", "1077"=> "신분당선");

$conn = new mysqli($credentials["host"], $credentials["user"], $credentials["pass"], $credentials["database"]);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8"); // 인코딩 박살 방지


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo $input_station;
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
	$station = $_POST["station_name"];
	$conn->query("insert into queries (a_query) values ('".$station."')");
	$conn->close();
	$idx = 0;
	$contents = [];
	$ch = curl_init();
	do{
		$url = "http://swopenapi.seoul.go.kr/api/subway/sample/xml/realtimeStationArrival/".(5*$idx+1)."/".(5*($idx+1))."/".rawurlencode($station);
		
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt ($ch, CURLOPT_ENCODING, "UTF-8"); // 한글 박살 방지
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$new = curl_exec($ch);
		$r = simplexml_load_string($new) or die("Error: Cannot create object");
		$idx++;
		array_push($contents, $r);
	} while($r->getName() != "RESULT");

	curl_close($ch);

	if (count($contents) <= 1){
		echo "<body><p>열차가 지금 안 다니고 있거나 역명을 잘못 입력하셨거나 그렇습니다.</p><p><a href='./aeogae.php'>다시하기</a></p><p><a href='./aeogae_queries.php'>지금까지의 검색어 감상</a></p></body>";
	}
	else{
		$return_string = '<h1><p id="r">';
		$st = preg_split("//u", $station.'역 열차들', -1, PREG_SPLIT_NO_EMPTY); // split unicode characters

		foreach($st as $a){
			$return_string .= '<span>'.$a.'</span>';
		}
		$return_string .= '</p></h1>';
		foreach($contents as $rs){
			$result = json_decode(json_encode($rs,JSON_UNESCAPED_UNICODE));
			if($result->row){
				if(is_array($result->row)){
					foreach($result->row as $obj){
				   		$return_string .= ('<p><i>'.$subwayid[$obj->subwayId].'</i> '.$obj->trainLineNm.': '.$obj->arvlMsg2.'</p>');
				   	}
				}
				else{ // 1개일 경우 decode 에서 array가 안되는 듯
					$return_string .= ('<p><i>'.$subwayid[$result->row->subwayId].'</i> '.$result->row->trainLineNm.': '.$result->row->arvlMsg2.'</p>');
				}
				
			}
			
		}
		$return_string .= ("<p><a href='./aeogae_queries.php'>지금까지의 검색어 감상</a></p><script>var colors = ".$greenish.";target = document.getElementById('r').children;var i;j = colors.length;len = target.length;inter = setInterval(function(){colors.unshift(colors.pop());for (i = 0; i < len; i++){target[i].style.color = colors[i%j];}}, 80);</script>");
		echo $return_string;
	}

}
?>
</div>

<?php include("footer.php"); ?>