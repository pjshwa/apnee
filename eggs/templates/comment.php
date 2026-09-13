<li id="comment_<?= $commentId ?>"<?php if (!$isReply): ?> onclick="toggleNestedCommentFormVisible(<?= $commentId ?>)"<?php endif; ?> class="imojify"><strong><?= escapeHtml($comment['commauthor']) ?>:</strong> <?= escapeHtml($comment['message']) ?> (<?= formatSeoulTime($comment['commdate']) ?>)<?php if (!empty($comment['commnew'])): ?><img class="comm_new_gif" src="../static/images/new.gif"/><?php endif; ?><?php if (!$isReply): ?><ul class="subcomments"><?php
foreach ($comment['subcomments'] ?? [] as $reply) {
    echo renderEggComment($reply, $articleId, true);
}
?></ul><?php endif; ?></li>
<?php
if (!$isReply) {
    echo renderEggReplyForm($articleId, $commentId);
}
