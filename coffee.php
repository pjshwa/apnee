<?php
require('db.php');
$total = $db->totalCoffee();
$average = round($db->avgCoffee(), 3);
?>

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
    <link rel="shortcut icon" href="./favicon.ico">
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
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.3/angular.min.js"></script>
    <script src="./static/js/ng-infinite-scroll.js"></script>
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
    <nav class="navbar" role="navigation" style="background-color:#85d0d3; border-color:#85d0d3;">
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
            <!-- Collect the nav links, forms, and other content for toggling -->
            <!-- /.navbar-collapse -->
        
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1" >
                <ul class="nav navbar-nav navbar-right">
                <li>
                    <a id="banner_inpadding" href = "./categories.php">해보세요</a>
                </li>
                </ul>
            </div>
        </div>
        <!-- /.container -->
    </nav>

<div class="container" style="margin-top:20px;" ng-app='coffee_infinite_load' ng-controller='CoffeeController'>
<p><span style="font-size: 1.3em; margin-right: 20px;">커피 일기</span><span style="font-size: 1em; float: right; border-style: solid; padding: 2px;"><img src="./static/pics/gov_three.gif" style="width: 20%; display: inline-block;"/>정보공개</span></p>
<div id="coffee_table" infinite-scroll='loadMore()' infinite-scroll-distance='1'>
<?php echo "<p><span>".$db->coffeeMax()."</span>일 간 총 <span style='color:blue;'>".$total."</span>잔의 아이스 그란데 아메리카노를 마셨다</p>";
echo "<p>평균 <span style='color:blue;'>".$average."</span>잔</p>"; ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>날짜</th>
            <th><img src="./static/pics/icedamericano.jpg" width="100"/></th>
        </tr>
    </thead>
    <tbody>
    	<tr ng-repeat="item in articles">
            <td>{{item.date}}</td>
            <td>{{item.iced_americano}}</td>
        </tr>
    </tbody>
</table>
</div>
<h4 id="loading_state">로딩중</h4>
</div>
<script>
var coffee_infinite_load = angular.module('coffee_infinite_load', ['infinite-scroll']);
coffee_infinite_load.controller('CoffeeController', function($scope) {
$scope.articles = <?php echo json_encode($db->getCoffee(0));?>;
var count = 20;
const max = <?php echo $db->coffeeMax();?>;
var busy = false;
$scope.loadMore = function() {
    if (busy) return;
    busy = true;
    if (count >= max) {
        document.getElementById('loading_state').innerHTML = '로딩 끝';
        return;
    }
    var xhttp = new XMLHttpRequest();
    var params = "start="+count;
    xhttp.open("POST", "coffee_get.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // for POST
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var resp = (JSON && JSON.parse(this.responseText) || $.parseJSON(this.responseText));
            resp.forEach(function(item, index){
                $scope.articles.push(item);
            });
            busy = false;
        }
    };
    count += 20;
    xhttp.send(params);

};
});

</script>
<?php include("footer.php"); ?>