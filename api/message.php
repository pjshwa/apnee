<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  $response_photo_src = false;
  $payload = json_decode(file_get_contents('php://input'), true);
  $response_text = '꿀륙';

  if ($payload['type'] == 'text') {
    $content = $payload['content'];
    if (preg_match('~^\d{1,3}$~', $content)) {
      $response_text = str_repeat('찍', (int)$content);
    }
    else {
      if (mt_rand(1, 10) < 5) {
        $response_text = '꽤액';
      }
      else {
        $response_text = '3자리 이내의 수를 입력해 보세요';
      }
    }
  }
  else if ($payload['type'] == 'photo') {
    $response_text = '쥑';
    $response_photo_src = $payload['content'];
  }

  $response = array("message" => array("text" => $response_text));
  if ($response_photo_src) {
    $response['message']['photo'] = array('url' => $response_photo_src, 'width' => 640, 'height' => 480);
  }
  echo json_encode($response);
}
else {
  header('Location: /index.php');
}
?>
