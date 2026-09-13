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

function loadClass($file, $name, $end) {
    $source = file_get_contents($file);
    $source = substr($source, strpos($source, 'class '.$name.' {'));
    $source = substr($source, 0, strpos($source, $end));
    eval($source);
}
loadClass($root.'/eggs/db.php', 'DB', '// Create a DB object');
$conn = new class {
    public $insert_id=101;
    public function prepare($sql) {return new class {public function bind_param(...$args) {} public function execute(){return true;} public function close(){}};}
    public function query($sql) {throw new RuntimeException('Global ID query would return another request ID');}
};
$reflection=new ReflectionClass('DB');
$db=$reflection->newInstanceWithoutConstructor();
$reflection->getProperty('mysqli')->setValue($db,$conn);
check($db->newComment(1,'author','comment',null)===101,'Wrong inserted ID');
$conn->insert_id=200;
check($db->newComment(1,'author','reply',101)===200,'Wrong nested comment ID');
echo 'comment-id: '.$checks.' checks passed'.PHP_EOL;
