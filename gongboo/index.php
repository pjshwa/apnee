<?php
require('db.php');
$category_set = $db->limitAllMemosByCategory(5);
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
				<a href="list.php"><h4>공부 페이지</h4></a>
				<?php
				if (count($category_set) === 0){
					echo '<div class="col-md-12"><ul><article><h4>글이 없어요</h4></article></ul></div>';
				}
				else {
					foreach($category_set as $category_data){
						echo '<div class="col-md-4">';
						echo '<h5>'.$category_data['title'].'</h5>';
						echo '<ul>';
						foreach($category_data['articles'] as $article){
							echo '<li><a href=./memo.php?id='.$article['id'].'>'.$article['title'].'</a></li>';
						}
						echo '</ul>';
						echo '<a href="list.php?category_id='.$category_data['id'].'"><h6>'.$category_data['title'].'에 대한 글 모두 보기</h6></a>';
						echo '</div>';
					}
				}
				?>
		</div>
	</div>
</body>
</html>