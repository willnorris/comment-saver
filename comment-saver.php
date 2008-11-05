<?php
/*
Plugin Name: Comment Saver
Description: Saves comment text in a temporary cookie before it is submitted.
Author: Will Norris
Author URI: http://willnorris.com/
Version: trunk
License: Dual GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html) and Modified BSD (http://www.fsf.org/licensing/licenses/index_html#ModifiedBSD)
*/

if  ( !class_exists('CommentSaver') ) {
	class CommentSaver {

		/** Get path for cookie. */
		function get_path() {
			$url_bits = parse_url(get_option('home'));
			$path = $url_bits['path'];
			return $path ? $path : '/';
		}

		/** Setup require javascript. */
		function js_setup() {
			if (is_single() || is_comments_popup()) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script('jquery.cookie', plugins_url('comment-saver') . '/jquery.cookie.min.js', array('jquery'));
			}
		}

		/** Add jQuery actions to save and restore comment. */
		function comment_form($id) { 
			$cookieName = 'comment_saver_post' . $id;
			$path = CommentSaver::get_path();
?>
			<script type="text/javascript">
				jQuery('#commentform').submit(function() {
					jQuery.cookie('<?php echo $cookieName;?>', jQuery('#comment').val(), {expires: (1/24), path: '<?php echo $path;?>'});
				});
				if (jQuery('#comment').val() == '') {
					jQuery('#comment').val(jQuery.cookie('<?php echo $cookieName;?>'));
				}
			</script>
		<?php }

		function remove_cookie($location, $comment) {
			$path = CommentSaver::get_path();

			setcookie('comment_saver_post' . $comment->comment_post_ID, null, -1, $path);
			return $location;
		}

	}
}

if (isset($wp_version)) {
	add_action( 'comment_form', array( 'CommentSaver', 'comment_form'));
	add_filter( 'comment_post_redirect', array( 'CommentSaver', 'remove_cookie'), 10, 2);
	add_action( 'wp_head', array( 'CommentSaver', 'js_setup'), 9);
}

?>
