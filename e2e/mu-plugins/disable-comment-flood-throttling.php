<?php

// When running the E2E tests, we know that we are flooding!
// https://github.com/WordPress/WordPress/blob/84e9601e5a2966c0aa80020bbf0c043dd8b6bfbb/wp-includes/default-filters.php#L299
remove_filter( 'comment_flood_filter', 'wp_throttle_comment_flood', 10 );
