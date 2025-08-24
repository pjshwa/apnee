<?php
require('db.php');
require('../consts/consts.php');

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
        foreach ($articles as $article) {
          $date = new DateTime($article['date'], new DateTimeZone('UTC'));
          $date->setTimezone($TIMEZONE);
          $datestr = $date->format('Y년 n월 j일 H시 i분');

          echo '<article id="article_'.$article['id'].'">';
          echo '<h2>'.$article['title'].'</h2>';
          echo '<h4>'.$datestr.'</h4>';
          echo '<br/>';
          echo '<div class="lead imojify" style="white-space: pre-wrap;">';
          echo $article['content'];

          $new_comment = false;
          $comments_count = count($article['comments']);
          foreach ($article['comments'] as $comment) {
            if ($comment['commnew']) {
              $new_comment = true;
            }
            if (isset($comment['subcomments'])) {
              $comments_count += count($comment['subcomments']);
              foreach ($comment['subcomments'] as $subcomment) {
                if ($subcomment['commnew']) {
                  $new_comment = true;
                }
              }
            }
          }

          echo '</div>';
          echo '<h4>';

          if ($new_comment) echo "<div class='btn btn-primary comments-indicator-button' ";
          else echo "<div class='btn btn-default comments-indicator-button' ";

          echo 'onclick="toggleCommentVisible('.$article['id'].')">댓글들 (<strong>'.$comments_count.'</strong>)</h4><div id="comments_for_article_'.$article['id'].'" style="display: none; padding: 0 20px;"><ul class="comments">';

          foreach ($article['comments'] as $comment) {
            $commdate = new DateTime($comment['commdate'], new DateTimeZone('UTC'));
            $commdate->setTimezone($TIMEZONE);
            $commdatestr = $commdate->format('Y-m-d H:i');

            echo '<li id="comment_'.$comment['commid'].'" onclick="toggleNestedCommentFormVisible('.$comment['commid'].')" class="imojify"><strong>'.htmlspecialchars($comment['commauthor']).':</strong> '.htmlspecialchars($comment['message']).' ('.$commdatestr.')';
            if ($comment['commnew']) echo '<img class="comm_new_gif" src="../static/images/new.gif"/>';

            echo '<ul class="subcomments">';
            if (isset($comment['subcomments'])) {
              foreach ($comment['subcomments'] as $subcomment) {
                echo '<li class="imojify"><strong>'.htmlspecialchars($subcomment['commauthor']).':</strong> '.htmlspecialchars($subcomment['message']).' ('.date('Y-m-d H:i', strtotime($subcomment['commdate'])).')';
                if ($subcomment['commnew']) echo '<img class="comm_new_gif" src="../static/images/new.gif"/>';
                echo '</li>';
              }
            }
            echo '</ul>';
            echo '</li>';

            // Comment form
            echo '<div id="nested_comment_form_for_comment_'.$comment['commid'].'" class="nested_comment_form_container js-nested-comment-form-container" style="display: none;">';
            echo '<form class="nested_comment_form">';
            echo '<input type="hidden" id="article_id" name="article_id" value="'.$article['id'].'"/>';
            echo '<input type="hidden" id="comment_id" name="comment_id" value="'.$comment['commid'].'"/>';
            echo '<h5>▲ 대댓글 달기</h5>';
            echo '<p>이름 <input type="text" class="nested_comment_author" name="comment_author" maxlength="30" required/></p>';
            echo '<p>내용 <input type="text" class="nested_comment_message" name="comment" maxlength="1000" required/></p>';
            echo '<p><input type="submit" class="btn btn-link" value="등록"/></p>';
            echo '</form>';
            echo '</div>';
          }

          echo '</ul><hr/>';
          echo '<form class="comment_form">';
          echo '<input type="hidden" id="article_id" name="article_id" value="'.$article['id'].'"/>';
        ?>
            <div class="row">
              <strong>이름 <input type="text" class="comment_author" name="comment_author" maxlength="30" required/></strong>
              <input type="submit" class="btn btn-link" value="댓글 등록하기"/>
            </div>
            <div class="row">
              내용
              <textarea class="form-control comment_message" name="comment" rows="3" maxlength="1000" required></textarea>
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
$choice = ($current_month + $current_year) % 6;
if ($choice == 0) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='40' height='40' patternTransform='scale(4) rotate(120)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(56,73%,61%, 1)'/><path d='M20-5V5m0 30v10m20-30v10M0 15v10'  stroke-linejoin='round' stroke-linecap='round' stroke-width='15' stroke='hsla(68,35%,90%, 1)' fill='none'/><path d='M-5 40H5M-5 0H5m30 0h10M35 40h10M15 20h10'  stroke-linejoin='round' stroke-linecap='round' stroke-width='15' stroke='hsla(144,49%,75%, 1)' fill='none'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } elseif ($choice == 1) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='40' height='40' patternTransform='scale(4) rotate(5)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(63,91%,78%, 0.4)'/><path d='M20 0L0 10v10l20-10zm0 10v10l20 10V20z'  stroke-width='4' stroke='none' fill='hsla(50,25%,46%, 0.4)'/><path d='M20-10V0l20 10V0zm0 30L0 30v10l20-10zm0 10v10l20 10V40z'  stroke-width='4' stroke='none' fill='hsla(21,51%,20%, 0.4)'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } elseif ($choice == 2) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='87' height='50.232' patternTransform='scale(3) rotate(170)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(151,19%,99%, 1)'/><path d='M0 54.424l14.5-8.373c4.813 2.767 9.705 5.573 14.5 8.37l14.5-8.373V29.303M0 4.193v16.744l-14.5 8.373L0 37.68l14.5-8.374V12.562l29-16.746m43.5 58.6l-14.5-8.37v-33.49c-4.795-2.797-9.687-5.603-14.5-8.37m43.5 25.111L87 37.67c-4.795-2.797-24.187-13.973-29-16.74l-14.5 8.373-14.5-8.37v-33.489m72.5 8.365L87 4.183l-14.5-8.37M87 4.183v16.745L58 37.673v16.744m0-66.976V4.185L43.5 12.56c-4.795-2.797-24.187-13.973-29-16.74L0 4.192l-14.5-8.37m29 33.484c4.813 2.767 9.705 5.573 14.5 8.37V54.42'  stroke-linejoin='round' stroke-linecap='round' stroke-width='1' stroke='hsla(179, 78%, 80%, 0.6)' fill='none'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } elseif ($choice == 3) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='70' height='70' patternTransform='scale(4) rotate(70)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(18,21%,92%, 1)'/><path d='M-4.793 4.438l8.788 12.156 12.156-8.79M8.42 62.57l6.408 2.818 2.817-6.408M62.644 27.187l2.746 1.208 1.207-2.746M31.998 30.542l-13.232 7.066 7.067 13.23M50.772 43.744l-4.859-5.038-5.038 4.86M59.713 62.882v3h3M-9.003 38.035l-3.81 14.508 14.508 3.809M54.983 27.574L52.625 16.83 41.88 19.189M26.88 23.931l4.838-5.058-5.058-4.838M4.838 25.543l-1.972 2.26 2.261 1.972M31.98-4.869l2.735 10.654L45.37 3.048M65.207 4.438l8.788 12.156 12.155-8.79M31.98 65.131l2.735 10.654 10.654-2.737M60.998 38.035l-3.811 14.508 14.508 3.809M12.778 46.169l-2.21-2.029-2.028 2.21M37.802 53.484l.556 2.948 2.948-.556'  stroke-linecap='square' stroke-width='3' stroke='hsla(341,85%,57%, 0.4)' fill='none'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
}
<?php } elseif ($choice == 4) { ?>
body {
  background-image: url("data:image/svg+xml,<svg id='patternId' width='100%' height='100%' xmlns='http://www.w3.org/2000/svg'><defs><pattern id='a' patternUnits='userSpaceOnUse' width='40' height='40' patternTransform='scale(5) rotate(15)'><rect x='0' y='0' width='100%' height='100%' fill='hsla(29, 0%, 100%, 1)'/><path d='M15 22h10m-10-4h10M35 2h10M35 38h10m-50 0H5M-5 2H5'  stroke-linecap='square' stroke-width='2' stroke='hsla(181,51%,64%, 0.6)' fill='none'/><path d='M18-5V5m4-10V5m16 10v10M18 35v10m4-10v10M2 15v10'  stroke-linecap='square' stroke-width='2' stroke='hsla(107,76%,85%, 1)' fill='none'/></pattern></defs><rect width='100%' height='100%' fill='url(%23a)'/></svg>");
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
.comm_new_gif {
  width: 35px;
  display: inline-block;
}
.nested_comment_form_container {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding-left: 20px;
  padding-top: 10px;
  margin: 10px 0;
}
@media only screen and (min-width: 320px) { 
  .main-container {
    margin: 75px 5px;
  }
}
@media only screen and (min-width: 769px) {
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

function toggleCommentVisible(article_id) {
  var comments_block = document.getElementById('comments_for_article_' + article_id);
  if (comments_block.style.display === 'block'){
    comments_block.style.display = 'none';
  }
  else if (comments_block.style.display === 'none'){
    comments_block.style.display = 'block';
  }
}

function toggleNestedCommentFormVisible(comment_id) {
  var form_block = document.getElementById('nested_comment_form_for_comment_' + comment_id);
  if (form_block.style.display === 'block') {
    form_block.style.display = 'none';
  }
  else if (form_block.style.display === 'none') {
    $('.js-nested-comment-form-container').hide();
    form_block.style.display = 'block';
  }
}

$("body").on('submit', 'form', function(event) {
  event.preventDefault();
  var $form = $(this);
  $.ajax({
    url: '/eggs/comment_post.php',
    type: 'post',
    data: $form.serialize(),
    success: function(res) {
      var $article = $form.closest('article');

      // Update comments count
      var $comment_indicator_button = $article.find('.comments-indicator-button');
      $comment_indicator_button.removeClass('btn-default').addClass('btn-primary');
      var current_comments_count = parseInt($comment_indicator_button.find('strong').text());
      $comment_indicator_button.find('strong').text(current_comments_count + 1);

      // Append new comment
      if ($form.hasClass('comment_form')) {
        $article.find('ul.comments').append(res);

        // Empty out inputs
        $form.find('input.comment_author').val('');
        $form.find('textarea.comment_message').val('');
      }
      else {
        var $comment_id = $form.find('input#comment_id').val();
        $article.find('li#comment_' + $comment_id).find('ul.subcomments').append(res);

        // Empty out inputs
        $form.find('input.nested_comment_author').val('');
        $form.find('input.nested_comment_message').val('');
      }
      imojify();

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(XMLHttpRequest.responseText);
    }
  });
});

imojify('.imojify', { ignore: '.ignore-emoji' });
</script>
</body>
</html>
