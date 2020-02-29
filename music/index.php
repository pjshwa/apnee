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
$is_first_month = $current_month == 2 && $current_year == 2020;
$prev_month = $current_month == 1 ? 12 : $current_month - 1;
$prev_year = $current_month == 1 ? $current_year - 1 : $current_year;
$next_month = $current_month == 12 ? 1 : $current_month + 1;
$next_year = $current_month == 12 ? $current_year + 1 : $current_year;
$is_danger_zone = ($now_month < $current_month && $now_year == $current_year) || ($now_year < $current_year) || (2 > $current_month && 2020 == $current_year) || (2020 > $current_year) || ($current_month > 12 || $current_month < 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>감상록</title>
	<link href="../static/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../static/css/imojify.css" rel="stylesheet" />
	<script src="../static/js/jquery.js"></script>
	<script src="../static/js/bootstrap.min.js"></script>
</head>
<?php
if ($is_danger_zone){
	header('Location: https://pjshwa.me/music/index.php');
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
							echo '<p class="lead imojify" style="white-space: pre-wrap;">';
							echo $article['content'];
							echo '</p><hr/></article>';
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
<script src="../static/js/imojify.js"></script>
<!-- source: https://github.com/danielthepope/imojify -->
<script>
imojify('.imojify', { ignore: '.ignore-emoji' });
</script>
</body>
</html>
