<?php
// Run with php -n tests/test_comment_rendering.php.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../eggs/comment_view.php';
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
function renderTestPage($file, $db) {
    // Substitute only the entry point's includes; use the real shared templates.
    $source = preg_replace('/\brequire(?:_once)?\b\s*(?:\([^;]*\)|[^;]+);/', '', file_get_contents($file));
    ob_start();
    eval('?>' . $source);
    return ob_get_clean();
}
function timestampFromFragment($html) {
    if (!preg_match('/\((\d{4}-\d\d-\d\d \d\d:\d\d)\)/', $html, $match)) {
        throw new RuntimeException('Timestamp missing');
    }
    return DateTimeImmutable::createFromFormat('!Y-m-d H:i', $match[1], new DateTimeZone(DISPLAY_TIMEZONE))
        ->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
}
$root = dirname(__DIR__);
$db = new class {
    public $id = 101;
    public $comments = [];
    public function newComment(...$args) { return $this->id; }
    public function getArticlesOfMonth($year, $month) {
        return [['id'=>7, 'title'=>'Article', 'content'=>'Body', 'date'=>'2026-09-13 00:00:00', 'comments'=>$this->comments]];
    }
};
$_SERVER['REQUEST_METHOD'] = 'POST';
$author = '<img src=x onerror=alert(1)>';
$message = '<script>alert(1)</script> & 한글';
$_POST = ['article_id'=>7, 'comment_author'=>$author, 'comment'=>$message];
$parentAjax = renderTestPage($root . '/eggs/comment_post.php', $db);
check(strpos($parentAjax, 'id="comment_101"') !== false, 'Parent ID missing');
check(strpos($parentAjax, 'toggleNestedCommentFormVisible(101)') !== false, 'Parent click handler missing');
check(strpos($parentAjax, 'name="article_id" value="7"') !== false, 'Article form ID changed');
check(strpos($parentAjax, 'name="comment_id" value="101"') !== false, 'Reply target changed');
check(strpos($parentAjax, '<ul class="subcomments"></ul>') !== false, 'Reply insertion container missing');
check(strpos($parentAjax, $author) === false && strpos($parentAjax, $message) === false, 'Unescaped user text');
check(strpos($parentAjax, escapeHtml($author)) !== false, 'Author was lost');
$parent = ['commid'=>101, 'commauthor'=>$author, 'message'=>$message,
           'commdate'=>timestampFromFragment($parentAjax), 'commnew'=>true, 'subcomments'=>[]];
$db->comments = [$parent];
$_GET = ['year'=>(int)gmdate('Y'), 'month'=>(int)gmdate('n')];
$page = renderTestPage($root . '/eggs/index.php', $db);
check(strpos($page, trim($parentAjax)) !== false, 'Page and AJAX parent fragments differ');
check(strpos($page, 'maxlength="30" required') !== false, 'Reply author validation changed');
check(strpos($page, 'maxlength="1000" required') !== false, 'Reply message validation changed');

$_POST['comment_id'] = 101;
$db->id = 102;
$replyAjax = renderTestPage($root . '/eggs/comment_post.php', $db);
check(strpos($replyAjax, 'id="comment_102"') !== false, 'Reply ID missing');
check(strpos($replyAjax, 'toggleNestedCommentFormVisible') === false, 'Reply must not have a parent handler');
check(strpos($replyAjax, '<form') === false && strpos($replyAjax, '<ul') === false, 'Reply introduced another nesting level');
check(strpos($replyAjax, 'comm_new_gif') !== false, 'New reply marker missing');
$reply = ['commid'=>102, 'commauthor'=>$author, 'message'=>$message,
          'commdate'=>timestampFromFragment($replyAjax), 'commnew'=>true];
$parent['subcomments'] = [$reply];
$db->comments = [$parent];
$page = renderTestPage($root . '/eggs/index.php', $db);
check(strpos($page, trim($replyAjax)) !== false, 'Page and AJAX reply fragments differ');
check(strpos($page, '댓글들 (<strong>2</strong>)') !== false, 'Comment count changed');

$parent['commnew'] = false;
$parent['subcomments'][0]['commnew'] = false;
$level = ob_get_level();
$fragment = renderEggComment($parent, 7);
check(ob_get_level() === $level, 'Rendering leaked an output buffer');
check(strpos($fragment, 'comm_new_gif') === false, 'Old comments marked new');
check(substr_count($fragment, 'class="nested_comment_form"') === 1, 'Duplicate reply forms');
$parent['commdate'] = '2026-09-12 18:30:00';
$parent['subcomments'][0]['commdate'] = $parent['commdate'];
check(substr_count(renderEggComment($parent, 7), '2026-09-13 03:30') === 2, 'Parent/reply date boundary differs');
echo 'comment-rendering: ' . $checks . " checks passed\n";
