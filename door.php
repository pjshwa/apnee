<html><head>

<title></title>
<link href="./static/css/bootstrap.min.css" rel="stylesheet" />
<script src= "./static/js/jquery.js"></script>
<script src="./static/js/bootstrap.min.js"></script>
<style>
body {
    background-color: rgb(0,0,0);
    color: white;
}
</style>
</head>
<body>
<div class="container">
<div class="row" style="margin-top: 40px;">
<form action="door.php" method="post" enctype="multipart/form-data">
<div class="col-sm-10"><input id="password" class="form-control" maxlength="100" name="password" required="required" title="" type="password" /></div>
<div class="col-sm-2"><button class="btn btn-success" type="submit">GO!</button></div>
</form>
</div></div>
<?php
session_start();
require('db.php');
if(isSet($_POST['password'])) {
	$r = $db->checklogin($_POST['password']);
    if($r) {
    	$_SESSION['username'] = $r['nickname'];
    	$_SESSION['user_id'] = $r['user_id'];
        $_SESSION['login'] = true;
    }
}

if(isSet($_SESSION['login'])) {
    header('Location: fishbowl.php');
}

?>

</body>
</html>