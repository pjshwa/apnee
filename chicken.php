<?php
require('db.php');
$now = new DateTime('now');
$chicken_counts = $db->getChicken();
$total = $db->totalChicken();
$now_year = $now->format('Y');
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
<p><span style="font-size: 1.3em; margin-right: 20px;">치킨 일기</span></p>
<div id="coffee_table">
<?php 
echo "<p><span>".$now_year."</span>년에는 몇 마리의 치킨을 먹었을까요?</p>";
echo "<p><span style='color:blue;'>".$total."</span>마리</p>";
?>
<p><a onclick="toggleFaqVisibility();"><span style='color:red;'>자주 묻는 질문</span></a></p>
<div id="chicken__faq" style="display: none;">
    <p style="white-space: pre-wrap;">1. 이런것을 왜 만들었나요?
    ● 슬플 때마다 치킨을 시켜먹는 버릇을 고치려고 만들었습니다.
2. 그럼 치킨을 안 먹고 대신 피자를 시켜 먹으면 어떡하나요?
    ● 피자는 남으면 나중에 데워 먹을수 있으니까 훨씬 낫습니다.
3. 호식이 두마리 치킨 같은건 몇 마리로 치나요?
    ● 두 마리로 칩니다.</p>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>날짜</th>
            <th><img src="./static/pics/chicken.png" width="100"/></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($chicken_counts as $chunk) {
            $start_date_of_week = new DateTime();
            $end_date_of_week = new DateTime();
            
            $start_date_of_week->setISODate((int)$chunk['year_of_week'], (int)$chunk['week']);
            $end_date_of_week->setISODate((int)$chunk['year_of_week'], (int)$chunk['week'], 7);
            echo "<tr>";
            echo "<td>".$start_date_of_week->format('Y-m-d')." ~ ".$end_date_of_week->format('Y-m-d')."</td>";
            echo "<td>".$chunk['chicken_sum_of_week']."</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
</div>
</div>
<script>
var faq_block = document.getElementById('chicken__faq');
function toggleFaqVisibility(article_id) {
	if (faq_block.style.display === 'block'){
		faq_block.style.display = 'none';
	}
	else if (faq_block.style.display === 'none'){
		faq_block.style.display = 'block';
	}
}
</script>

<?php include("footer.php"); ?>