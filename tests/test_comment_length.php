<?php
// Run with php -n tests/test_comment_length.php.
// Isolate legacy templates/classes from their eager DB connections and use
// database substitutes; no credentials or live database are required.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
ob_start();
$root = dirname(__DIR__);
require_once $root . '/eggs/comment_view.php';
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
function renderSource($file, $vars = []) {
    extract($vars);
    $source = preg_replace('/\brequire(?:_once)?\b\s*(?:\([^;]*\)|[^;]+);/', '', file_get_contents($file));
    ob_start(); eval('?>'.$source); return ob_get_clean();
}

$_SERVER['REQUEST_METHOD']='POST';
$db=new class {
    public $calls=0;
    public function newComment(...$args){$this->calls++; return 101;}
};
$cases=[
    [str_repeat('가',11),'test',200],
    [str_repeat('가',30),str_repeat('나',1000),200],
    [str_repeat('가',31),'test',400],
    ['name',str_repeat('나',1001),400],
    [str_repeat('a',30),str_repeat('b',1000),200],
    [str_repeat('a',31),'test',400],
    ['name',str_repeat("나\n",500),200],
    ['name',str_repeat("나\n",500).'x',400],
    ['😀','emoji 😀',200],
    [[1],'test',400],
    ['name',[1],400],
    ["\xff",'test',400],
    ['name',"\xc3\x28",400]
];
foreach ($cases as [$author,$comment,$expected]) {
    $_POST=['article_id'=>1,'comment_author'=>$author,'comment'=>$comment];
    http_response_code(200);
    $before=$db->calls;
    $html=renderSource($root.'/eggs/comment_post.php',['db'=>$db,'TIMEZONE'=>new DateTimeZone('Asia/Seoul')]);
    check(http_response_code()===$expected,'Unexpected comment response');
    check($db->calls-$before===($expected===200?1:0),'Invalid input reached database');
}
echo 'comment-length: '.$checks.' checks passed'.PHP_EOL;
