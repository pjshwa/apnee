<?php
// Run with php -n tests/test_view_helpers.php.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../lib/view_helpers.php';
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
foreach ([
    ['<script>alert("x")</script>', '&lt;script&gt;alert(&quot;x&quot;)&lt;/script&gt;'],
    ["a' & b\"", 'a&#039; &amp; b&quot;'],
    ['한글 😀', '한글 😀'],
    ['&amp;', '&amp;amp;'],
    [null, ''], [0, '0'], ["a\xffb", "a\xef\xbf\xbdb"]
] as [$input, $expected]) {
    check(escapeHtml($input) === $expected, 'HTML escaping changed');
}
$original = date_default_timezone_get();
foreach (['UTC', 'America/Los_Angeles', 'Asia/Seoul'] as $timezone) {
    date_default_timezone_set($timezone);
    check(formatSeoulTime('2026-09-12 18:30:00') === '2026-09-13 03:30', 'Date boundary conversion failed');
    check(formatSeoulTime('2025-12-31 18:30:45', 'Y-m-d H:i:s') === '2026-01-01 03:30:45', 'Year boundary or seconds changed');
    check(formatSeoulTime('2026-09-13 03:00:00', 'Y년 n월 j일 H시 i분') === '2026년 9월 13일 12시 00분', 'Article format changed');
    check(date_default_timezone_get() === $timezone, 'Formatting changed the server timezone');
}
date_default_timezone_set($original);
echo 'view-helpers: ' . $checks . " checks passed\n";
