<?php
require('db.php');
if (isSet($_GET['id'])){
	$id = (int)$_GET['id'];
}
else {
	header('Location: crema/index.php');
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

	<title>크리마 메모 - <?php echo $memo['title'];?></title>
	<link href="../static/css/bootstrap.min.css" rel="stylesheet" />
	<script src="../static/js/jquery.js"></script>
	<script src="../static/js/bootstrap.min.js"></script>
</head>

<body style="margin-top: 75px;">
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