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
    $date = date('Y-m-d H:i');
    $db->newComment($article_id, $author, $comment, $comment_id);

    if ($comment_id) {
  
      echo '<li class="imojify"><strong>';
      echo htmlspecialchars($author);
      echo ':</strong> ';
      echo htmlspecialchars($comment);
      echo ' ('.$date.')';
  
      // New posted comment is always... new
      echo '<img class="comm_new_gif" src="../static/images/new.gif"/>';
      echo '</li>';

    }
    else {

      echo '<li onclick="toggleNestedCommentFormVisible('.$comment['commid'].')" class="imojify"><strong>';
      echo htmlspecialchars($author);
      echo ':</strong> ';
      echo htmlspecialchars($comment);
      echo ' ('.$date.')';
  
      // New posted comment is always... new
      echo '<img class="comm_new_gif" src="../static/images/new.gif"/>';
      echo '</li>';

    }
  }
}
?>
