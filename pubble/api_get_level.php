<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$level_id = $_GET['id'] ?? null;

if (!$level_id) {
    echo json_encode(['success' => false, 'error' => 'Level ID required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT level_id, name, description, level_data, plays, likes, created_at
        FROM pubble_levels
        WHERE level_id = ?
    ");
    
    $stmt->execute([$level_id]);
    $level = $stmt->fetch();
    
    if (!$level) {
        echo json_encode(['success' => false, 'error' => 'Level not found']);
        exit;
    }
    
    // 플레이 횟수 증가
    $stmt = $pdo->prepare("UPDATE pubble_levels SET plays = plays + 1 WHERE level_id = ?");
    $stmt->execute([$level_id]);
    
    echo json_encode([
        'success' => true,
        'level' => [
            'id' => $level['level_id'],
            'name' => $level['name'],
            'description' => $level['description'],
            'data' => json_decode($level['level_data'], true),
            'plays' => $level['plays'] + 1,
            'likes' => $level['likes'],
            'created_at' => $level['created_at']
        ]
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
