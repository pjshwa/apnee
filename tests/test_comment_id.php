<?php
// Run with php -n tests/test_comment_id.php.
// Isolate legacy templates/classes from their eager DB connections and use
// database substitutes; no credentials or live database are required.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
ob_start();
$root = dirname(__DIR__);
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}

require_once $root.'/eggs/EggRepository.php';
$conn = new class {
    public $insert_id=101;
    public function prepare($sql) {return new class {public function bind_param(...$args) {} public function execute(){return true;} public function close(){}};}
    public function query($sql) {throw new RuntimeException('Global ID query would return another request ID');}
};
$db = new EggRepository($conn);
check($db->newComment(1,'author','comment',null)===101,'Wrong inserted ID');
$conn->insert_id=200;
check($db->newComment(1,'author','reply',101)===200,'Wrong nested comment ID');
echo 'comment-id: '.$checks.' checks passed'.PHP_EOL;
