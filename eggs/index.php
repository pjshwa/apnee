<?php
require('db.php');
$now = new DateTime('now');
$now_month = $now->format('m');
$now_year = $now->format('Y');

$current_month = (int)($_GET['month'] ?? $now_month);
$current_year = (int)($_GET['year'] ?? $now_year);

$is_last_month = $now_month == $current_month && $now_year == $current_year;
$is_first_month = $current_month == 4 && $current_year == 2017;
$prev_month = $current_month == 1 ? 12 : $current_month - 1;
$prev_year = $current_month == 1 ? $current_year - 1 : $current_year;
$next_month = $current_month == 12 ? 1 : $current_month + 1;
$next_year = $current_month == 12 ? $current_year + 1 : $current_year;
$is_danger_zone = ($now_month < $current_month && $now_year == $current_year) || ($now_year < $current_year) || (4 > $current_month && 2017 == $current_year) || (2017 > $current_year) || ($current_month > 12 || $current_month < 1);

if ($is_danger_zone){
  echo '<script>alert("ㅎㅎ 숫자 이상한걸로 바꾸지마 바보야");';
  echo "window.location.href='https://www.youtube.com/watch?v=dQw4w9WgXcQ';</script>";
}
else {
  $articles = $db->getArticlesOfMonth($current_year, $current_month);
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
<body>
  <div class="main-container">
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
            if ($comment['commnew']) {
              $new_comment = true;
              break;
            }
          }
          echo '</div>';
          echo '<h4>';
          if ($new_comment) echo "<div class='btn btn-primary comments-indicator-button' ";
          else echo "<div class='btn btn-default comments-indicator-button' ";
          echo 'onclick="toggleScriptVisibility('.$article['id'].')">댓글들 (<strong>'.count($article['comments']).'</strong>)</h4><div id="comments_for_article_'.$article['id'].'" style="display: none; padding: 0 20px;"><ul class="comments">';
          foreach($article['comments'] as $comment){
            echo '<li class="imojify"><strong>'.htmlspecialchars($comment['commauthor']).':</strong> '.htmlspecialchars($comment['comment']).' ('.date('Y-m-d', strtotime($comment['commdate'])).')';
            if ($comment['commnew']) echo '<img id="comm_new_gif" src="../static/pics/new.gif"/>';
            echo '</li>';
          }
          echo '</ul><hr/>';
          echo '<form id="comment_post_'.$article['id'].'" name="comment_post_'.$article['id'].'">';
          echo '<input type="hidden" id="article_id" name="article_id" value="'.$article['id'].'"/>';
        ?>
            <div class="row">
              <strong>이름 <input type="text" id="comment_author" name="comment_author" maxlength="30" required/></strong>
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
<?php
$choice = ($current_month + $current_year) % 3;
if ($choice == 0) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='40' height='40' patternTransform='scale(4) rotate(120)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(56,73%,61%, 1)'/><path d='M20-5V5m0 30v10m20-30v10M0 15v10'  stroke-linejoin='round' stroke-linecap='round' stroke-width='15' stroke='hsla(68,35%,90%, 1)' fill='none'/><path d='M-5 40H5M-5 0H5m30 0h10M35 40h10M15 20h10'  stroke-linejoin='round' stroke-linecap='round' stroke-width='15' stroke='hsla(144,49%,75%, 1)' fill='none'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } elseif ($choice == 1) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='40' height='40' patternTransform='scale(4) rotate(5)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(63,91%,78%, 0.4)'/><path d='M20 0L0 10v10l20-10zm0 10v10l20 10V20z'  stroke-width='4' stroke='none' fill='hsla(50,25%,46%, 0.4)'/><path d='M20-10V0l20 10V0zm0 30L0 30v10l20-10zm0 10v10l20 10V40z'  stroke-width='4' stroke='none' fill='hsla(21,51%,20%, 0.4)'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } else { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='40' height='40' patternTransform='scale(5) rotate(50)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(112,100%,59%, 0.4)'/><path d='M20 0L0 10v10l20-10zm0 10v10l20 10V20z'  stroke-width='1' stroke='none' fill='hsla(85,86%,85%, 0.4)'/><path d='M20-10V0l20 10V0zm0 30L0 30v10l20-10zm0 10v10l20 10V40z'  stroke-width='1' stroke='none' fill='hsla(228,98%,55%, 0.4)'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } ?>
html {
  height: auto;
  min-height: 100%;
}
.main-container {
  background: white;
  margin: 75px 0;
  padding: 20px;
}
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
.highlight {
  background-color: #cbf09c;
}
@media only screen and (min-width: 320px) { 
  #comm_new_gif {
    width: 10%;
    display: inline-block;
  }
  .main-container {
    margin: 75px 5px;
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
  .main-container {
    margin: 75px;
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

$('form').on('submit', function(event) {
  event.preventDefault();
  var $form = $(this);
  $.ajax({
    url: '/eggs/comment_post.php',
    type: 'post',
    data: $form.serialize(),
    success: function(res) {
      var $article = $form.closest('article');

      // Append new comment
      $article.find('ul.comments').append(res);

      // Update comments count
      var $comment_indicator_button = $article.find('.comments-indicator-button');
      $comment_indicator_button.removeClass('btn-default').addClass('btn-primary');
      var current_comments_count = parseInt($comment_indicator_button.find('strong').text());
      $comment_indicator_button.find('strong').text(current_comments_count + 1);

      // Empty out inputs
      $form.find('input#comment_author').val('');
      $form.find('textarea#comment').val('');
    }
  });
});

imojify('.imojify', { ignore: '.ignore-emoji' });
</script>
</body>
</html>
