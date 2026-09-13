<?php
/** Create an independent connection; including this file never connects to MySQL. */
function connectDatabase($config = null) {
    if ($config === null) {
        require __DIR__ . '/../credentials.php';
        $config = $credentials;
    }

    // Use the same exception behavior on every supported PHP version.
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli($config['host'], $config['user'], $config['pass'], $config['database']);
    $connection->set_charset('utf8mb4');
    return $connection;
}

/** Feature repositories share connection injection, not their SQL or result shapes. */
abstract class DatabaseRepository {
    protected $mysqli;

    public function __construct($connection) {
        $this->mysqli = $connection;
    }
}
