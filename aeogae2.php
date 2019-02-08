<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title></title>

    <!-- Bootstrap Core CSS -->
    <link href="./static/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="./static/css/half-slider.css" rel="stylesheet" />
    <link href="./static/css/notice.css" rel="stylesheet" />
    <link rel="shortcut icon" href="./favicon.ico"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery -->
    <script src= "./static/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="./static/js/bootstrap.min.js"></script>
    <script src="./static/js/angular.min.js"></script>
    <!-- button lil script-->
    <script src="https://use.typekit.net/aty5yfo.js"></script>
    <script>try{Typekit.load({ async: true });}catch(e){}</script>
    <style>
    body {
        font-family: myriad-pro;
    }
    </style>

</head>
<body>

    <!-- Navigation -->
    <nav class="navbar" role="navigation" style="background-image: linear-gradient(120deg, #89f7fe 0%, #66a6ff 100%);">
        <div class="container banner_padding" >
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="pull-left" href= "/"><div id="logo" style="width: 55px;height: 55px;background-size: 100% 100%;background-repeat: no-repeat;background-image:url('./static/pics/duck.png');"></div></a>
            </div>
        
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" >
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a id="banner_inpadding" href = "./categories.php">해보세요</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<div class="container" style="margin-top:20px;" ng-app='aeogae_ajax' ng-controller='AeogaeController'>
<p>
<span style="font-size: 1.3em; margin-right: 20px;">애오개 2</span>
</p>
<form>
<div class="col-sm-10">
  <input type="text" class="form-control" id="content" name="content" placeholder="역명을 입력하세요" value='애오개'/>
</div>
<div class="col-sm-2">
  <button type="submit" ng-click="countData()" class="btn btn-default">알려줘</button>
</div>
</form>
<p id="station_name" style="margin-bottom: 20px; margin-top: 60px;"></p>
<p>속도 개선을 위한 노력을 하고 있습니다</p>
<table class="table table-striped">
    <thead>
        <tr>
            <th>노선</th>
            <th>방향</th>
            <th>도착</th>
        </tr>
    </thead>
    <tbody>
    	<tr ng-repeat="item in articles">
            <td>{{item.lineName}}</td>
            <td>{{item.trainLineNm}}</td>
            <td>{{item.arvlMsg2}}</td>
        </tr>
    </tbody>
</table>
</div>
</div>
<script>

const subway_id = {
"1001": "1호선", "1002": "2호선", "1003": "3호선", "1004": "4호선", "1005": "5호선", "1006": "6호선", "1007": "7호선", "1008": "8호선", "1009": "9호선", "1063": "경의중앙선", "1065": "공항철도", "1067": "경춘선", "1071": "수인선", "1075": "분당선", "1077": "신분당선"
};

var aeogae = angular.module('aeogae_ajax', []);
aeogae.controller('AeogaeController', function($scope) {
$scope.articles = [];
$scope.getSubway = function(repeats, station){
    for(var i = 0; i < repeats; i++){
        var xhttp = new XMLHttpRequest();
        var params = 'start='+(5*i+1)+'&end='+(5*(i+1))+'&station='+station;
        xhttp.open("POST", "aeogae2_get.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // for POST
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var resp = (JSON && JSON.parse(this.responseText) || $.parseJSON(this.responseText)); // parse
                if(resp.hasOwnProperty("realtimeArrivalList")){
                    resp["realtimeArrivalList"].forEach(function(item, index){
                        item["lineName"] = subway_id[item["subwayId"]]; // sub with text
                        $scope.articles.push(item);
                    });
                    $scope.$apply(); // force apply
                }
            }
        };
        xhttp.send(params);
    }
};

$scope.countData = function(){
    $scope.articles.length = 0; // array clear
    var xhttp = new XMLHttpRequest();
    var station = document.getElementById('content').value;
    document.getElementById('content').value = '';
    document.getElementById('station_name').innerHTML = station + '역 열차들';
    var params = 'start=1&end=1&station='+station;
    xhttp.open("POST", "aeogae2_get.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // for POST
    xhttp.onreadystatechange = (function(callback) {
        return function () {
            if (this.readyState == 4 && this.status == 200) {
                var resp = (JSON && JSON.parse(this.responseText) || $.parseJSON(this.responseText));
                if(resp.hasOwnProperty("errorMessage")){ // if not, no trains
                    var counts = resp["errorMessage"]["total"];
                    result = Math.floor(counts / 5) + 1;
                    callback(result, station);// calls getSubway
                }
                else {
                    $scope.articles.push({'lineName':'데이터가', 'trainLineNm': '하나도', 'arvlMsg2': '없다'});
                    $scope.$apply();
                }
            }
        }
    })($scope.getSubway);
    xhttp.send(params);
};



});

</script>

<?php include("footer.php"); ?>
