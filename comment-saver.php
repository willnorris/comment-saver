<?php
/**
 * Plugin Name: Comment Saver
 * Description: Saves comment text in a temporary cookie before it is submitted.
 * Author: Will Norris
 * Plugin URI: http://wordpress.org/extend/plugins/comment-saver/
 * Author URI: http://willnorris.com/
 * Version: 1.6
 * License: Dual GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html) and Modified BSD (http://www.fsf.org/licensing/licenses/index_html#ModifiedBSD)
 *
 * @package comment-saver
 */

add_filter( 'comment_post_redirect', 'comment_saver_cleanup', 10, 2 );
add_action( 'wp_enqueue_scripts', 'comment_saver_js' );

/**
 * Get path for comment saver cookie.
 */
function comment_saver_cookie_path() {
	$parts = wp_parse_url( get_option( 'home' ) );
	$path  = array_key_exists( 'path', $parts ) ? $parts['path'] : '/';
	return $path;
}

/**
 * Setup require javascript.
 */
function comment_saver_js() {
	if ( ! is_singular() ) {
		return;
	}

	wp_enqueue_script( 'comment-saver', plugin_dir_url( __FILE__ ) . 'comment-saver.js', array(), '1.6', true );
	wp_localize_script(
		'comment-saver',
		'comment_saver_cookie',
		array(
			'name' => 'comment_saver_post' . get_the_ID(),
			'path' => comment_saver_cookie_path(),
		)
	);
}

/**
 * Cleanup comment saver cookie.
 *
 * @param string     $location The 'redirect_to' URI sent via $_POST.
 * @param WP_Comment $comment Comment object.
 *
 * @return string
 */
function comment_saver_cleanup( $location, $comment ) {
	$path = comment_saver_cookie_path();
	setcookie( 'comment_saver_post' . $comment->comment_post_ID, '', -1, $path );
	return $location;
}
