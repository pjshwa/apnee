<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = ($page - 1) * $limit;

$sort = $_GET['sort'] ?? 'recent'; // recent, popular, likes

$order_by = 'created_at DESC';
if ($sort === 'popular') {
    $order_by = 'plays DESC';
} elseif ($sort === 'likes') {
    $order_by = 'likes DESC';
}

try {
    // 전체 레벨 수 조회
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pubble_levels");
    $total = $stmt->fetch()['total'];
    
    // 레벨 목록 조회
    $stmt = $pdo->prepare("
        SELECT level_id, name, description, plays, likes, created_at
        FROM pubble_levels
        ORDER BY $order_by
        LIMIT $limit OFFSET $offset
    ");
    
    $stmt->execute();
    $levels = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'levels' => $levels,
        'total' => $total,
        'page' => $page,
        'total_pages' => ceil($total / $limit)
    ]);
    
} catch(PDOException $e) {
    error_log('DB Error in api_list_levels.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
