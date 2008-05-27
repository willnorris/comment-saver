<?php
/*
Plugin Name: Comment Saver
Description: Saves comment text before it is submitted.
Author: Will Norris
Author URI: http://willnorris.com/
Version: trunk
License: Dual GPL (http://www.fsf.org/licensing/licenses/info/GPLv2.html) and Modified BSD (http://www.fsf.org/licensing/licenses/index_html#ModifiedBSD)
*/

if  ( !class_exists('CommentSaver') ) {
	class CommentSaver {

		/** Setup require javascript. */
		function js_setup() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script('jquery.cookie', '/wp-content/plugins/comment-saver/jquery.cookie.js', array('jquery'));
		}

		/** Add jQuery actions to save and restore comment. */
		function comment_form($id) { 
			$cookieName = 'comment_saver_post' . $id;
			$path = parse_url(get_option('home'), PHP_URL_PATH);
			$path = $path ? $path : '/';
?>
			<script type="text/javascript">
				/*
				jQuery('#commentform').submit(function() {
					jQuery.cookie('<?php echo $cookieName;?>', jQuery('#comment').val(), {expires: (1/24), path: '<?php echo $path;?>'});
				});
				*/
				if (jQuery('#comment').val() == '') {
					jQuery('#comment').val(jQuery.cookie('<?php echo $cookieName;?>'));
				}
			</script>
		<?php }

		function remove_cookie($location, $comment) {
			$path = parse_url(get_option('home'), PHP_URL_PATH);
			$path = $path ? $path : '/';

			setcookie('comment_saver_post' . $comment->comment_post_ID, null, -1, $path);
			return $location;
		}

		function set_cookie($comment) {
			echo "setting cookie"; exit;
			$path = parse_url(get_option('home'), PHP_URL_PATH);
			$path = $path ? $path : '/';
			$exp_time = time() + 3600;

			setcookie('comment_saver_post' . $comment['comment_post_ID'], $comment['comment_content'], $exp_time, $path);
		}
	}
}

if (isset($wp_version)) {
	add_action( 'comment_form', array( 'CommentSaver', 'comment_form'));
	add_action( 'preprocess_comment', array( 'CommentSaver', 'set_cookie' ), -999 );
	add_filter( 'comment_post_redirect', array( 'CommentSaver', 'remove_cookie'), 10, 2);
	add_action( 'wp_head', array( 'CommentSaver', 'js_setup'), 9);
}

?>
