<?php
require_once(dirname(__FILE__).'/../credentials.php');

// DB 연결 설정 (공용 credentials 사용)
$db_host = $credentials['host'];
$db_port = isset($credentials['port']) ? $credentials['port'] : '3306';
$db_name = $credentials['database'];
$db_user = $credentials['user'];
$db_pass = $credentials['pass'];

try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log('DB Connection Error: ' . $e->getMessage());
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]));
}
