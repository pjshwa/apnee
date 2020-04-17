<?php
header('Content-Type: application/json');
$ch = curl_init();
$url = "http://swopenapi.seoul.go.kr/api/subway/sample/json/realtimeStationArrival/".$_POST['start']."/".$_POST['end']."/".rawurlencode($_POST['station']);

curl_setopt ($ch, CURLOPT_URL, $url);
curl_setopt ($ch, CURLOPT_ENCODING, "UTF-8"); // 한글 박살 방지
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
echo curl_exec($ch);
?>
