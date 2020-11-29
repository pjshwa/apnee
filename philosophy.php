
<?php
require('db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $db->insertPhiChat($_POST['content']);
    header("Location: " . $_SERVER['REQUEST_URI']);
}
else {
    $items = $db->getPhiChat();
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

    <title>철학자 채팅</title>
    <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet">

    <!-- Bootstrap Core CSS -->
    <link href="./static/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="./static/css/app.css" rel="stylesheet" />
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
    <script src="https://use.typekit.net/aty5yfo.js"></script>
    <script>try{Typekit.load({ async: true });}catch(e){}</script>
    <style>
    body {
        font-family: myriad-pro;
    }

.article-list-vertical{
    font-family: 'Oswald', sans-serif;
    list-style:none;
    margin: 0 auto;
    max-width: 100%;
    text-align: center;
    padding: 0;
}

.article-list-vertical li{
    display: block;
    box-sizing:border-box;
    border-radius: 3px;
    text-align: left;
    box-shadow: 1px 3px 1px 0 rgba(0, 0, 0, 0.08);
    border:1px solid #cfcfcf;
    overflow: hidden;
    background-color: #fff;

    max-width: 100%;
    margin: 20px;
}

/* Article photo */

.article-list-vertical li > a{
    float: left;
    width: 15%;
    padding-top: 15%;
    display: block;

    background-size: cover;
}

.article-list-vertical li div{
    float: left;
    box-sizing: border-box;

    max-width: 80%;
    padding: 10px;
}

/* Article title */

.article-list-vertical li div h2{
    font-size: 1.3em;
    word-wrap: break-word;
    margin:0;
    padding-top: 10px;
}
/* Article excerpt */

.article-list-vertical li div p{
    line-height: 20px;
    color: #5d5d5d;
    font-size: 150%;
    margin: 0;
}

@media (max-width: 600px){

    .article-list-vertical li{
        max-width: 100%;
        margin: 5px;

    }
    .article-list-vertical li > a{
        float: right;
        width: 40%;
        padding-top: 40%;
    }

    .article-list-vertical li div{
        float: left;
        max-width: 55%;
        padding: 10px;
        font-size:80%;
        word-wrap: break-word;
    }
    .article-list-vertical li div h2{
        word-wrap: break-word;
    }

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
                <a class="pull-left" href= "/"><div id="logo" style="width: 55px;height: 55px;background-size: 100% 100%;background-repeat: no-repeat;background-image:url('./static/images/duck.png');"></div></a>
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
<div class="container">
<ul class="article-list-vertical">
<?php
    foreach($items as $item){
        echo '<li>';
        echo '<a href="'.$item['src'].'" style="background-image: url('."'".$item['img_src']."'".')"></a>';
        echo '<div>';
        echo '<p>"'.$item['content'].'"</p>';
        echo '<h2>'.$item['description'].'</h2>';
        echo '</div></li>';
    }
?>
</ul>
<form class="form-horizontal" action="philosophy.php" method="post">
    <div class="row" style="margin-top:30px;">
    <div class="col-sm-10">
      <input type="text" class="form-control" id="content" name="content" placeholder="내용 입력"/>
    </div>
    <div class="col-sm-2">
      <button type="submit" class="btn btn-default">Submit</button>
    </div>
    </div>
</form>
</div>
<?php include("footer.php"); ?>