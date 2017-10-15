<?php
session_start();
if(isSet($_SESSION['login'])) {
require('db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>어항</title>

    <!-- Bootstrap Core CSS -->
    <link href="./static/css/bootstrap.min.css" rel="stylesheet" />
    <link href="./static/css/fish_style.css" rel="stylesheet" />
    <link rel="shortcut icon" href="./favicon.ico">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery -->
    <script src= "./static/js/jquery.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.3/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.0-beta.2/angular-sanitize.js"></script>
    <script src="./static/js/ng-infinite-scroll.js"></script>
    
</head>
<body>
<div class="container" ng-app='fish_infinite_load' ng-controller='FishController'>
<p><?php echo $_SESSION['username'].'님 안녕'; ?></p>
<div class="row" style="float:right;">
	<a href="fishbowl_new.php" class="btn btn-primary">글써글써</a><a href="index.php" class="btn btn-danger">뿌셔뿌셔</a>
</div>
	<div class="row">
		<div class="[ col-xs-12 col-sm-offset-2 col-sm-8 ]" infinite-scroll='loadMore()' infinite-scroll-distance='2'>
			<ul class="event-list">
				<li ng-repeat="item in articles">
					<time>
						<span class="month">{{item.month}}</span>
						<span class="day">{{item.day}}</span>
						<span class="year">{{item.year}}</span>
						<span class="time">ALL DAY</span>
					</time>
					<form id="comment" name="comment" method="post">
						<input type="hidden" name="a_id" ng-value="item.article_id"/>
						<input type="hidden" name="a_comment"/>
					</form>
					<div class="info">
						<h2 class="title">{{item.title}}</h2>
						<p class="desc" style="white-space: pre-wrap;" ng-bind-html="item.content | trusted"></p>
					</div>
				</li>
			</ul>
		</div>
	</div>
	<h4 id="loading_state">Loading...</h4>
</div>
<script>
var fish_infinite_load = angular.module('fish_infinite_load', ['infinite-scroll', 'ngSanitize']);
// fish_infinite_load.filter('split', function() {
//     return function(input, splitChar, splitIndex) {
//         // do some bounds checking here to ensure it has that index
//         return input.split(splitChar)[splitIndex];
//     }
// });
fish_infinite_load.filter('trusted', function($sce) { return $sce.trustAsHtml; });
fish_infinite_load.controller('FishController', function($scope) {
$scope.articles = <?php echo json_encode($db->getLogs(0));?>;
var count = 10;
const max = <?php echo $db->fishMax();?>;
var busy = false;
$scope.loadMore = function() {
	if (busy) return;
	busy = true;
	if (count >= max) {
		document.getElementById('loading_state').innerHTML = 'End of articles';
		return;
	}
	var xhttp = new XMLHttpRequest();
	var params = "start="+count;
	xhttp.open("POST", "fishbowl_get.php", true);
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
	count += 10;
	xhttp.send(params);

};
});

function comment(){
	var c = prompt('덧글을 입력하세요.');
	if(c != '') {
		// post this
	}
}

</script>
</body>

</html>


<?php
}
else{
	header('Location: index.php');
}

?>