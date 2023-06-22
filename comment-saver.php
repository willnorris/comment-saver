<?php
/**
 * Plugin Name: Comment Saver
 * Description: Saves comment text in a temporary cookie before it is submitted.
 * Author: Will Norris
 * Plugin URI: http://wordpress.org/extend/plugins/comment-saver/
 * Author URI: http://willnorris.com/
 * Version: 1.4
 * License: Dual GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html) and Modified BSD (http://www.fsf.org/licensing/licenses/index_html#ModifiedBSD)
 *
 * @package comment-saver
 */

add_action( 'comment_form', 'comment_saver_form' );
add_filter( 'comment_post_redirect', 'comment_saver_cleanup', 10, 2 );
add_action( 'wp', 'comment_saver_js' );

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
	if ( is_single() ) {
		wp_enqueue_script( 'jquery.cookie', plugins_url( 'comment-saver/jquery.cookie.min.js' ), array( 'jquery' ), '1.0', true );
	}
}

/**
 * Add jQuery actions to save and restore comment.
 *
 * @param int $id The post ID.
 */
function comment_saver_form( $id ) {
	$cookie_name = 'comment_saver_post' . $id;
	$path        = comment_saver_cookie_path();

	echo '
	<script type="text/javascript">
		jQuery(function() {
			jQuery("#commentform").submit(function() {
				jQuery.cookie("' . $cookie_name . '", jQuery("#comment").val(), {expires: (1/24), path: "' . $path . '"});
			});
			if (jQuery("#comment").val() == "") {
				var cookieValue = jQuery.cookie("' . $cookie_name . '");
				if (cookieValue != null) {
					jQuery("#comment").val(cookieValue));
				}
			}
		});
	</script>';
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
	setcookie( 'comment_saver_post' . $comment->comment_post_ID, null, -1, $path );
	return $location;
}
