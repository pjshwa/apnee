<?php
// Run with php -n tests/test_reflected_xss.php.
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
function loadClass($file, $name, $end) {
    $source = file_get_contents($file);
    $source = substr($source, strpos($source, 'class '.$name.' {'));
    $source = substr($source, 0, strpos($source, $end));
    eval($source);
}
loadClass($root.'/foretooth/catalog.php', 'Page', '$page = new Page();');
$page = new Page();
$db = new class {
    public $correct;
    public function getanswer($id) { return 'answer'; }
    public function updateTries($id,$correct) {$this->correct=$correct;}
};
foreach ([null, '', '0', '-1', '1"><script>alert(1)</script>', "1';alert(1);//", ['1'], '9999999999999999999999999', '1', '12'] as $value) {
    $_GET = ['page'=>$value];
    $expected = in_array($value, ['1','12'], true) ? (int)$value : 1;
    $normalized = $page->getPageParam('page');
    check($normalized === $expected, 'Unexpected normalized page');
    ob_start();
    $page->displayOneItem(['id'=>1,'tries'=>0,'corrects'=>0,'question'=>'test'], $normalized);
    $html=ob_get_clean();
    check(strpos($html, 'name="q_page" value="'.$expected.'"') !== false, 'Unsafe page field');
    foreach (['answer', 'wrong'] as $answer) {
        $_SERVER['REQUEST_METHOD']='POST';
        $_POST=['q_key'=>1,'ans'=>$answer,'q_page'=>$value];
        $html=renderSource($root.'/foretooth/check.php',['db'=>$db]);
        check(strpos($html,"location.href='catalog.php?page=".$expected."';")!==false,'Unsafe redirect');
        check($db->correct === ($answer === 'answer'), 'Answer handling changed');
        check(strpos($html, $answer==='answer'?'맞았다':'틀렸다')!==false,'Feedback missing');
    }
}
echo 'reflected-xss: '.$checks.' checks passed'.PHP_EOL;
