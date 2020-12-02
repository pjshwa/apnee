<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require('db.php');
  $article_id = $_POST["article_id"];
  $author = $_POST["comment_author"];
  $comment = $_POST["comment"];
  $date = date('Y-m-d H:i');
  $db->newComment($article_id, $author, $comment);

  echo '<li class="imojify"><strong>';
  echo htmlspecialchars($author);
  echo ':</strong> ';
  echo htmlspecialchars($comment);
  echo ' ('.$date.')';

  // New posted comment is always... new
  echo '<img id="comm_new_gif" src="../static/images/new.gif"/>';
  echo '</li>';
}
?>
