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
    <!-- button lil script-->

</head>
<style>
<?php
	if(mt_rand(0,1) == 0){
		echo "body{background-color:#006;}";
	}
	else{
		echo "body{background-color:#036;}";
	}
?>
#snow{
background: none;
font-family: Androgyne;
background-image: url('http://www.wearewebstars.dk/codepen/img/s1.png'), url('http://www.wearewebstars.dk/codepen/img//s2.png'), url('http://www.wearewebstars.dk/codepen/img//s3.png');
height: 100%;
left: 0;
position: absolute;
top: 0;
width: 100%;
z-index:1;
-webkit-animation: snow 10s linear infinite;
-moz-animation: snow 10s linear infinite;
-ms-animation: snow 10s linear infinite;
animation: snow 10s linear infinite;
}
@keyframes snow {
  0% {background-position: 0px 0px, 0px 0px, 0px 0px;}
  50% {background-position: 500px 500px, 100px 200px, -100px 150px;}
  100% {background-position: 500px 1000px, 200px 400px, -100px 300px;}
}
@-moz-keyframes snow {
  0% {background-position: 0px 0px, 0px 0px, 0px 0px;}
  50% {background-position: 500px 500px, 100px 200px, -100px 150px;}
  100% {background-position: 400px 1000px, 200px 400px, 100px 300px;}
}
@-webkit-keyframes snow {
  0% {background-position: 0px 0px, 0px 0px, 0px 0px;}
  50% {background-position: 500px 500px, 100px 200px, -100px 150px;}
  100% {background-position: 500px 1000px, 200px 400px, -100px 300px;}
}
@-ms-keyframes snow {
  0% {background-position: 0px 0px, 0px 0px, 0px 0px;}
  50% {background-position: 500px 500px, 100px 200px, -100px 150px;}
  100% {background-position: 500px 1000px, 200px 400px, -100px 300px;}
}	

</style>
<body>
<div id="snow"><?php
  $rand = mt_rand(0,2);
	if($rand == 0){
		echo '<audio autoplay="true" controls src="./m/icecavechant.mp3">';
	}
	elseif($rand == 1){
		echo '<audio autoplay="true" controls src="./m/dance.mp3">';
	}
  else{
    echo '<audio autoplay="true" controls src="./m/mous.mp3">';
  }

?></audio></div></body></html>