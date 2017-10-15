<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $db->newProjectItem($_POST['content']);
    echo "<script>var loc = window.location;window.location = loc.protocol + '//' + loc.host + loc.pathname + loc.search;</script>"; // force get
}
else {
    $items = $db->getProjectItems();
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

<div class="container" style="margin-top:20px;">
<p><span style="font-size: 1.3em; margin-right: 20px;">프로젝트 일지</span></p>
<ul>
    <?php foreach($items as $item){ ?>
    <li style="white-space: pre-wrap; margin-top: 8px;"><?php echo $item['content']; ?></li>
    <?php } ?>
</ul>
<form class="form-horizontal" action="a.php" method="post">
    <div class="row" style="margin-top:30px;">
    <div class="col-sm-10">
      <textarea id="content" class="text" cols="86" rows ="10" name="content"></textarea>
    </div>
    <div class="col-sm-2">
      <button type="submit" class="btn btn-default">Submit</button>
    </div>
    </div>
</form>
</div>
<?php include("footer.php"); ?>