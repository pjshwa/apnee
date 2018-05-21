<?php
require('db.php');
if (isSet($_GET['id'])){
	$id = (int)$_GET['id'];
}
else {
	header('Location: gongboo/index.php');
}
$memo = $db->memoOfArticle($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title><?php echo $memo['title'];?></title>
	<link href="../static/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Inconsolata" />
	<script src="../static/js/jquery.js"></script>
	<script src="../static/js/bootstrap.min.js"></script>
	<?php
	if ($memo['include_highlighter'] == 1){
		echo "<link rel='stylesheet' href='../static/css/prism.css'>";
		echo "<script src= '../static/js/prism.js'></script>"; // with erlang, elixir
		echo "<script>hljs.initHighlightingOnLoad();</script>";
		echo "<script src='https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.2/MathJax.js?config=TeX-MML-AM_CHTML'></script>";
	}
	?>
</head>

<body style="margin-top: 75px; font-family: Inconsolata; font-size: 18px;">
	<!-- Body -->
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<article>
          <?php
					echo '<h2>'.$memo['title'].'</h2>';
					echo '<h4>'.date('Y년 n월 j일', strtotime($memo['date'])).'</h4>';
					echo '<br/><p class="lead" style="white-space: pre-wrap;">';
					echo $memo['content'];
          echo '</p>';
          ?>
					<hr/>
				</article>
			</div>
		</div>
	</div>
</body>
</html>