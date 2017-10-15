<?php session_start();
if(isSet($_SESSION['login'])) {
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $db->newFish($_SESSION['user_id'], $_POST['title'], $_POST['content']);
    echo "<script>window.location.href='./fishbowl.php';</script>"; //force get
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>어항 글쓰기</title>

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
    
</head>
<body>
<div class="container">
<p><?php echo $_SESSION['username'].'님 안녕'; ?></p>
<div class="row" style="float:right;">
	<a href="fishbowl_new.php" class="btn btn-primary">글써글써</a><a href="index.php" class="btn btn-danger">뿌셔뿌셔</a>
</div>
<div class="row" style="padding-top: 40px;">
<form action="fishbowl_new.php" method="post">
  <div class="form-group">
    <p>제목:</p>
    <input type="text" class="form-control" id="title" name="title">
  </div>
  <div class="form-group">
    <p>내용:</p>
    <textarea id="content" class="text" cols="86" rows ="10" name="content"></textarea>
  </div>
  <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</div>
</body>

</html>

<?php
}
else{
    header('Location: index.php');
} ?>