<?php
require('db.php');
$now = new DateTime('now');
$now_month = (int)$now->format('m');
$now_year = (int)$now->format('Y');
if (isSet($_GET['month'])){
	$current_month = (int)$_GET['month'];
}
else {
	$current_month = $now_month;
}
if (isSet($_GET['year'])){
	$current_year = (int)$_GET['year'];
}
else {
	$current_year = $now_year;
}
$articles = $db->articlesOfMonth($current_year, $current_month);
$is_last_month = $now_month == $current_month && $now_year == $current_year;
$is_first_month = $current_month == 4 && $current_year == 2017;
$prev_month = $current_month == 1 ? 12 : $current_month - 1;
$prev_year = $current_month == 1 ? $current_year - 1 : $current_year;
$next_month = $current_month == 12 ? 1 : $current_month + 1;
$next_year = $current_month == 12 ? $current_year + 1 : $current_year;
$is_danger_zone = ($now_month < $current_month && $now_year == $current_year) || ($now_year < $current_year) || (4 > $current_month && 2017 == $current_year) || (2017 > $current_year) || ($current_month > 12 || $current_month < 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>달걀 페이지</title>
	<link href="../static/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../static/css/imojify.css" rel="stylesheet" />
	<script src="../static/js/jquery.js"></script>
	<script src="../static/js/bootstrap.min.js"></script>
</head>
<?php
if ($is_danger_zone){
	echo '<script>alert("ㅎㅎ 숫자 이상한걸로 바꾸지마 바보야");';
	echo "window.location.href='https://www.youtube.com/watch?v=dQw4w9WgXcQ';</script>";
}
else {
?>
<body style="margin-top: 75px;">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<?php
				if (count($articles) === 0){
					if ($current_month == $now_month && $current_year == $now_year){
						echo '<article><h4>'.$current_month.'월에는 아직 글이 없어요</h4></article>';
					}
					else {
						echo '<article><h4>'.$current_month.'월에는 글이 없어요</h4></article>';
					}
				}
				else {
				foreach($articles as $article){
					echo '<article id="article_'.$article['id'].'">';
					echo '<h2>'.$article['title'].'</h2>';
					echo '<h4>'.date('Y년 n월 j일', strtotime($article['date'])).'</h4>';
					echo '<br/>';
					echo '<div class="lead imojify" style="white-space: pre-wrap;">';
					echo $article['content'];
					$new_comment = false;
					foreach($article['comments'] as $comment){
						if ($comment['commnew']) $new_comment = true;
					}
					echo '</div><div class="container"><div class="row"><h4>';
					if ($new_comment) echo "<div class='btn btn-primary' ";
					else echo "<div class='btn btn-default' ";
					echo 'onclick="toggleScriptVisibility('.$article['id'].')">댓글들 (<strong>'.count($article['comments']).'</strong>)</div></h4></div><div id="comments_for_article_'.$article['id'].'" style="display:none;"><ul>';
					foreach($article['comments'] as $comment){
						echo '<li class="imojify"><strong>'.htmlspecialchars($comment['commauthor']).':</strong> '.htmlspecialchars($comment['comment']).' ('.date('Y-m-d', strtotime($comment['commdate'])).')';
						if ($comment['commnew']){
							echo '<img id="comm_new_gif" src="../static/pics/new.gif"/>';
						}
						echo '</li>';
					}
					echo '</ul><hr/>';
					echo '<form id="comment_post_'.$article['id'].'" name="comment_post_'.$article['id'].'" action="./comment_post.php" method="post">';
					echo '<input type="hidden" id="cyear" name="cyear" value="'.$current_year.'"/>';
					echo '<input type="hidden" id="cmonth" name="cmonth" value="'.$current_month.'"/>';
					echo '<input type="hidden" id="article_id" name="article_id" value="'.$article['id'].'"/>';
				?>
						<div class="row">
							<strong>이름 <input type="text" id="comment_author" name="comment_author" maxlength="30" required/></strong>
							<input type="hidden" id="comment_password" name="comment_password" value="뭘봐" maxlength="100"/>
						<input type="submit" class="btn btn-link" value="댓글 등록하기"/>
						</div>
						<div class="row">
							내용
							<textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
						</div>
					</form>
				<?php
					echo '</div><hr/></article>';
				}
			}
				?>
				<ul class="pager">
					<?php
					if ($is_first_month) {
						echo '<li class="previous disabled"><a href="#">&larr; 저번 달</a></li>';
					}
					else {
						echo sprintf('<li class="previous"><a href="index.php?year=%d&month=%d">&larr; 저번 달</a></li>', $prev_year, $prev_month);
					}
					if ($is_last_month) {
						echo '<li class="next disabled"><a href="#">다음 달 &rarr;</a></li>';
					}
					else {
						echo sprintf('<li class="next"><a href="index.php?year=%d&month=%d">다음 달 &rarr;</a></li>', $next_year, $next_month);
					}
					}?>
				</ul>
			</div>
		</div>
	</div>
<style>
.video-container {
	position: relative;
	width: 100%;
	height: 0;
	padding-bottom: 56.25%;
}
.video {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
}
@media only screen and (min-width: 320px) { 
	#comm_new_gif {
		width: 10%;
		display: inline-block;
	}
}
@media only screen and (min-width: 769px) { 
	#comm_new_gif {
		width: 5%;
		display: inline-block;
	}
	.video-container {
		width: 768px;
		padding-bottom: 432px;
	}
}
</style>
<script src="../static/js/imojify.js"></script>
<!-- source: https://github.com/danielthepope/imojify -->
<script>

function toggleScriptVisibility(article_id) {
	var comments_block = document.getElementById('comments_for_article_' + article_id);
	if (comments_block.style.display === 'block'){
		comments_block.style.display = 'none';
	}
	else if (comments_block.style.display === 'none'){
		comments_block.style.display = 'block';
	}
}

imojify('.imojify', { ignore: '.ignore-emoji' });
</script>
</body>
</html>
