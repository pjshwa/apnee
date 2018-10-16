<?php
require('db.php');
if (isSet($_GET['category_id'])){
  $category_id = (int)$_GET['category_id'];
}
else {
  $category_id = 0;
}
$result = $db->titlesOfMemo($category_id);
$category_title = $result[0];
$articles = $result[1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>공부</title>
	<link href="../static/css/bootstrap.min.css" rel="stylesheet" />
	<script src="../static/js/jquery.js"></script>
	<script src="../static/js/bootstrap.min.js"></script>
</head>
<body style="margin-top: 75px;">
	<!-- Body -->
	<div class="container">
		<div class="row">
      <?php echo '<h4>'.$category_title.'에 대한 글 ('.count($articles).')</h4>'; ?>
			<div class="col-md-12">
				<ul>
				<?php
				if (count($articles) === 0){
					echo '<article><h4>글이 하나도 없어요</h4></article>';
				}
				else {
					foreach($articles as $article){
						if ($article['raw_link'] == 1) {
							echo '<li><a href="'.$category_id.'/'.$article['title'].'.html">'.$article['title'].'</a> ('.date('Y년 n월 j일', strtotime($article['date'])).')</li>';
						}
						else {
							echo '<li><a href=./memo.php?id='.$article['id'].'>'.$article['title'].'</a> ('.date('Y년 n월 j일', strtotime($article['date'])).')</li>';
						}
					}
				}?>
				</ul>
			</div>
		</div>
	</div>
</body>
</html>