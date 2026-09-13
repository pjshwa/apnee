<?php
require_once __DIR__ . '/../lib/view_helpers.php';

/** Render the same comment fragment for page loads and AJAX inserts. */
function renderEggComment(array $comment, $articleId, $isReply = false) {
    $commentId = (int)$comment['commid'];
    ob_start();
    require __DIR__ . '/templates/comment.php';
    return ob_get_clean();
}

function renderEggReplyForm($articleId, $commentId) {
    $articleId = (int)$articleId;
    $commentId = (int)$commentId;
    ob_start();
    require __DIR__ . '/templates/reply_form.php';
    return ob_get_clean();
}
