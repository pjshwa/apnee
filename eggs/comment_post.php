<?php
require_once __DIR__ . '/comment_view.php';

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
    $createdAt = gmdate('Y-m-d H:i:s');
    $nid = $db->newComment($article_id, $author, $comment, $comment_id);
    echo renderEggComment([
      'commid' => $nid,
      'commauthor' => $author,
      'message' => $comment,
      'commdate' => $createdAt,
      'commnew' => true,
      'subcomments' => []
    ], $article_id, $comment_id != null);
  }
}
?>
