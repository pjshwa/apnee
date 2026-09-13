<?php
// Run with php -n tests/test_database.php (uses a stand-in mysqli driver).
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (extension_loaded('mysqli')) { fwrite(STDERR, "Run with php -n.\n"); exit(1); }
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
if (!function_exists('mysqli_report')) {
    define('MYSQLI_REPORT_ERROR', 1);
    define('MYSQLI_REPORT_STRICT', 2);
    function mysqli_report($flags) { $GLOBALS['driver_events'][] = ['report', $flags]; }
}
class mysqli {
    public static $failure = '';
    public $rows = [];
    public $statements = [];
    public $insert_id = 101;
    public function __construct(...$args) {
        $GLOBALS['driver_events'][] = ['connect', $args];
        if (self::$failure === 'connect') throw new RuntimeException('connect failed');
    }
    public function set_charset($charset) {
        $GLOBALS['driver_events'][] = ['charset', $charset];
        if (self::$failure === 'charset') throw new RuntimeException('charset failed');
        return true;
    }
    public function prepare($sql) {
        if (self::$failure === 'prepare') throw new RuntimeException('prepare failed');
        $statement = new DatabaseTestStatement($sql, array_shift($this->rows) ?? []);
        $this->statements[] = $statement;
        return $statement;
    }
}
class DatabaseTestStatement {
    public $sql;
    public $params = [];
    public $types;
    private $rows;
    private $bound = [];
    public function __construct($sql, $rows) { $this->sql = $sql; $this->rows = $rows; }
    public function bind_param($types, &...$params) { $this->types = $types; $this->params = $params; }
    public function bind_result(&...$values) { $this->bound = $values; }
    public function execute() {
        if (mysqli::$failure === 'execute') throw new RuntimeException('execute failed');
        return true;
    }
    public function fetch() {
        if (!$this->rows) return false;
        foreach (array_shift($this->rows) as $i => $value) $this->bound[$i] = $value;
        return true;
    }
    public function close() {}
}
$GLOBALS['driver_events'] = [];
$root = dirname(__DIR__);
foreach (['SiteRepository.php', 'eggs/EggRepository.php', 'cats/CatRepository.php',
          'uncle/UncleRepository.php', 'important/FoodRepository.php', 'foretooth/JudgeRepository.php'] as $file) {
    require_once $root . '/' . $file;
}
check($GLOBALS['driver_events'] === [], 'Loading repositories must not open a database');
$config = ['host'=>'test-host','user'=>'test-user','pass'=>'test-pass','database'=>'test-db'];
$connection = connectDatabase($config);
check($GLOBALS['driver_events'] === [
    ['report', MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT],
    ['connect', array_values($config)], ['charset', 'utf8mb4']
], 'Connection configuration or ordering changed');
check(connectDatabase($config) !== $connection, 'Connections must have independent lifetimes');
foreach (['connect', 'charset', 'prepare', 'execute'] as $failure) {
    mysqli::$failure = $failure;
    try {
        $candidate = connectDatabase($config);
        (new CatRepository($candidate))->catCount();
        throw new LogicException('Expected failure was swallowed');
    } catch (RuntimeException $error) {
        check($error->getMessage() === $failure . ' failed', 'Driver error was changed');
    }
}
mysqli::$failure = '';
foreach ([['CatRepository','catCount','getCats'], ['UncleRepository','picCount','getPic']] as [$class,$count,$list]) {
    $connection->rows = [[[2]], [['제목','image.jpg','2026-09-13']]];
    $repository = new $class($connection);
    check($repository->$count() === 2, $class . ' count changed');
    check($repository->$list(10) === [['title'=>'제목','img_src'=>'image.jpg','date'=>'2026-09-13']], $class . ' rows changed');
}
$connection->rows = [[[1]], [['식당','food.jpg',1,'comment','review','2026-09-13']]];
$food = new FoodRepository($connection);
check($food->picCount(1) === 1, 'Food count changed');
check($food->getFood(1,0)[0]['review'] === 'review', 'Food row changed');
$connection->rows = [[[3,'question',2,1]], [['answer']], [[6]]];
$judge = new JudgeRepository($connection);
check($judge->getItems(2) === [['id'=>3,'question'=>'question','tries'=>2,'corrects'=>1]], 'Judge row changed');
$statement = end($connection->statements);
check($statement->params === [5], 'Page offset changed');
check($judge->getanswer(3) === 'answer', 'Answer changed');
check($judge->getTotalPage() === 6, 'Question count changed');
$connection->rows = [[['title','content','2026-09-13']], [['nickname',1,20,100,'2026-09-13']]];
$site = new SiteRepository($connection);
check($site->getEventById(1)['content'] === 'content', 'Event row changed');
check($site->getPika()[0]['remain_time'] === 20, 'Score row changed');
$connection->rows = [
    [[1,'article','body','2026-09-13 00:00:00']],
    [[2,1,'parent','message','2026-09-13 00:00:00']],
    [[3,2,'reply','response','2026-09-13 01:00:00']]
];
$eggs = new EggRepository($connection);
$articles = $eggs->getArticlesOfMonth(2026,9);
check($articles[0]['comments'][0]['commid'] === 2, 'Parent comment association changed');
check($articles[0]['comments'][0]['subcomments'][0]['commid'] === 3, 'Reply association changed');
check($eggs->newComment(1,'author','new message',null) === 101, 'Inserted comment ID changed');
echo 'database: ' . $checks . " checks passed\n";
