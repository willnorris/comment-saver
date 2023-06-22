<?php
/**
 * Disables the comment flood throttling
 *
 * When running the E2E this, we could post comments quickly.
 * https://github.com/WordPress/WordPress/blob/84e9601e5a2966c0aa80020bbf0c043dd8b6bfbb/wp-includes/default-filters.php#L299
 *
 * @package comment-saver-e2e-test
 */

remove_filter( 'comment_flood_filter', 'wp_throttle_comment_flood', 10 );
