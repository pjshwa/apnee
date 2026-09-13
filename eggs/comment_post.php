<?php
require_once __DIR__ . '/../lib/view_helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $article_id = $_POST["article_id"];
  $author = $_POST["comment_author"] ?? '';
  $comment = $_POST["comment"] ?? '';
  $comment_id = $_POST["comment_id"] ?? null;

  // Count Unicode characters, including newlines, without requiring mbstring.
  $author_length = is_string($author) ? preg_match_all('/./us', $author) : false;
  $comment_length = is_string($comment) ? preg_match_all('/./us', $comment) : false;

  if ($author_length === false || $comment_length === false) {
    http_response_code(400);
    echo '올바른 UTF-8 문자열을 입력해주세요.';
  }
  else if ($author_length > 30) {
    http_response_code(400);
    echo '이름이 너무 기네요!';
  }
  else if ($comment_length > 1000) {
    http_response_code(400);
    echo '댓글이 너무 기네요! 좀 줄여주세요.';
  }
  else {
    require('db.php');
    $date = formatSeoulTime('now');
    $nid = $db->newComment($article_id, $author, $comment, $comment_id);

    echo '<li id="comment_'.$nid.'" ';
    if ($comment_id == null) {
      echo 'onclick="toggleNestedCommentFormVisible('.$nid.')" ';
    }
    echo 'class="imojify"><strong>';
    echo escapeHtml($author);
    echo ':</strong> ';
    echo escapeHtml($comment);
    echo ' ('.$date.')';

    echo '<img class="comm_new_gif" src="../static/images/new.gif"/>';
    
    if ($comment_id == null) {
      echo '<ul class="subcomments"></ul>';
    }

    echo '</li>';

    if ($comment_id == null) {

      // Comment form
      echo '<div id="nested_comment_form_for_comment_'.$nid.'" class="nested_comment_form_container js-nested-comment-form-container" style="display: none;">';
      echo '<form class="nested_comment_form">';
      echo '<input type="hidden" id="article_id" name="article_id" value="'.$article_id.'"/>';
      echo '<input type="hidden" id="comment_id" name="comment_id" value="'.$nid.'"/>';
      echo '<h5>▲ 대댓글 달기</h5>';
      echo '<p>이름 <input type="text" class="nested_comment_author" name="comment_author" maxlength="30" required/></p>';
      echo '<p>내용 <input type="text" class="nested_comment_message" name="comment" maxlength="1000" required/></p>';
      echo '<p><input type="submit" class="btn btn-link" value="등록"/></p>';
      echo '</form>';
      echo '</div>';
    }
  }
}
?>
