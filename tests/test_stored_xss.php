<?php
// Run with php -n tests/test_stored_xss.php.
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
function renderSource($file, $vars = []) {
    extract($vars);
    $source = preg_replace('/require\([^;]*\);/', '', file_get_contents($file));
    ob_start(); eval('?>'.$source); return ob_get_clean();
}

class mysqli {
    public $connect_error = null;
    public function __construct(...$args) {}
    public function set_charset($value) {}
    public function close() {}
    public function query($sql) {
        return new class {
            public $num_rows = 1;
            private $fetched = false;
            public function fetch_assoc() {
                if ($this->fetched) return null;
                $this->fetched = true;
                return ['a_query'=>$GLOBALS['payload'], 'reg_date'=>'2026-09-13 03:00:00'];
            }
        };
    }
}
$_SERVER['REQUEST_METHOD'] = 'GET';
$db = new class {
    public function getPhiChat() {return [['src'=>'#','img_src'=>'x','content'=>$GLOBALS['payload'],'description'=>'x']];}
    public function getPika() {return [['nickname'=>$GLOBALS['payload'],'success'=>1,'remain_time'=>1,'hits_score'=>1,'date'=>'2026-09-13']];}
};
foreach (['<script>alert("review")</script>', '<img src=x onerror=alert(1)>', '한글 & "quote" \'single\''] as $payload) {
    foreach (['philosophy.php','pika_leaderboard.php','aeogae_queries.php'] as $file) {
        $html = renderSource($root.'/'.$file, ['db'=>$db,'credentials'=>array_fill_keys(['host','user','pass','database'],'test'),'TIMEZONE'=>new DateTimeZone('Asia/Seoul')]);
        check(strpos($html, htmlspecialchars($payload, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) !== false, $file.' did not escape input');
        check(strpos($html, $payload) === false, $file.' contains raw input');
    }
}
echo 'stored-xss: '.$checks.' checks passed'.PHP_EOL;
