<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

// POST 데이터 받기
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['level_data'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$name = $input['name'] ?? 'Untitled Level';
$description = $input['description'] ?? '';
$level_data = json_encode($input['level_data']);

// 랜덤한 8자리 ID 생성
function generateLevelId($pdo) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $max_attempts = 10;
    
    for ($i = 0; $i < $max_attempts; $i++) {
        $level_id = '';
        for ($j = 0; $j < 8; $j++) {
            $level_id .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        // 중복 확인
        $stmt = $pdo->prepare("SELECT id FROM pubble_levels WHERE level_id = ?");
        $stmt->execute([$level_id]);
        
        if (!$stmt->fetch()) {
            return $level_id;
        }
    }
    
    return null;
}

try {
    $level_id = generateLevelId($pdo);
    
    if (!$level_id) {
        echo json_encode(['success' => false, 'error' => 'Failed to generate unique ID']);
        exit;
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO pubble_levels (level_id, name, description, level_data)
        VALUES (?, ?, ?, ?)
    ");
    
    $stmt->execute([$level_id, $name, $description, $level_data]);
    
    echo json_encode([
        'success' => true,
        'level_id' => $level_id,
        'url' => '/pubble/play.php?id=' . $level_id
    ]);
    
} catch(PDOException $e) {
    error_log('DB Error in api_save_level.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
