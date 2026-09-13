<?php
require_once __DIR__ . '/../consts/consts.php';

/** Escape text or a quoted HTML attribute; not a JavaScript or URL encoder. */
function escapeHtml($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Format a UTC database timestamp in the site's display timezone. */
function formatSeoulTime($utcTimestamp, $format = 'Y-m-d H:i') {
    $date = new DateTimeImmutable($utcTimestamp, new DateTimeZone('UTC'));
    return $date->setTimezone(new DateTimeZone(DISPLAY_TIMEZONE))->format($format);
}
