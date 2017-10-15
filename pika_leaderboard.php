<?php
require('db.php');
$items = $db->getPika();
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
<h3>피카추 리더 보드</h3>
<p>베타 테스트를 해야 하는데 한가한 사람이 없다</p>
<table class="table table-striped">
    <thead>
        <tr>
            <th>이름</th>
            <th>성공</th>
            <th>기록</th>
            <th>시간</th>
        </tr>
    </thead>
    <tbody>
    	<?php
        foreach($items as $item){
            echo "<tr>";
            echo "<td>".$item['nickname']."</td>";
            echo "<td>";
            if($item['success'] == '1') echo "<span style='color:green;'>O</span></td><td>남은 시간: <span style='color:blue;'>".$item['remain_time']."</span>초";
            else echo "<span style='color:red;'>X</span></td><td>클릭 횟수: ".$item['hits_score']."회";
            echo "</td>";
            echo "<td>".$item['date']."</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</div>

    <footer class="footer-footer" style="margin-bottom:0;">
        <div class="container">
            <div class="col-sm-8">
                <div class="footer-address">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="footer-policy-group--web">
                    <a class="footer-link footer-link--right" href="https://www.youtube.com/watch?v=PWmfNeLs7fA">개인정보 취급방침</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
