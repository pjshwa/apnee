<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require('db.php');
  $article_id = $_POST["article_id"];
  $author = $_POST["comment_author"];
  $comment = $_POST["comment"];
  $comment_id = $_POST["comment_id"];

  // Exception handling..?
  if (strlen($author) > 30) {
    echo '이름이 너무 기네요!';
    http_response_code(400);
  }
  else if (strlen($comment) > 1000) {
    echo '댓글이 너무 기네요! 좀 줄여주세요.';
    http_response_code(400);
  }
  else {
    $date = new DateTime('now', new DateTimeZone('Asia/Seoul'));
    $nid = $db->newComment($article_id, $author, $comment, $comment_id);

    echo '<li id="comment_'.$nid.'" ';
    if ($comment_id == null) {
      echo 'onclick="toggleNestedCommentFormVisible('.$nid.')" ';
    }
    echo 'class="imojify"><strong>';
    echo htmlspecialchars($author);
    echo ':</strong> ';
    echo htmlspecialchars($comment);
    echo ' ('.$date->format('Y-m-d H:i').')';

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
