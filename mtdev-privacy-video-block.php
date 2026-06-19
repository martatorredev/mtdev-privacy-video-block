<?php
/**
 * Plugin Name:       MTDev Privacy Video Block
 * Plugin URI:        https://github.com/martatorredev/mtdev-privacy-video-block
 * Description:       Gutenberg block for embedding YouTube videos without tracking cookies (youtube-nocookie.com). Privacy-first, GDPR/CCPA & WCAG 2.2 friendly.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Marta Torre
 * Author URI:        https://martatorre.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mtdev-privacy-video-block
 *
 * @package MTDev\PrivacyVideoBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'MTDEVPVB_VERSION', '1.0.0' );
define( 'MTDEVPVB_FILE', __FILE__ );
define( 'MTDEVPVB_DIR', plugin_dir_path( __FILE__ ) );
define( 'MTDEVPVB_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register the block from the compiled metadata in /build.
 *
 * @return void
 */
function mtdevpvb_register_block() {
	$build = MTDEVPVB_DIR . 'build';

	if ( ! file_exists( $build . '/block.json' ) ) {
		add_action(
			'admin_notices',
			function () {
				echo '<div class="notice notice-error"><p>';
				echo esc_html__( 'MTDev Privacy Video Block: run "npm install && npm run build" before activating.', 'mtdev-privacy-video-block' );
				echo '</p></div>';
			}
		);
		return;
	}

	register_block_type( $build );
}
add_action( 'init', 'mtdevpvb_register_block' );

/**
 * Extract a YouTube video ID from any common URL format.
 *
 * @param string $url Raw URL.
 * @return string Video ID or empty string.
 */
function mtdevpvb_extract_video_id( $url ) {
	if ( empty( $url ) ) {
		return '';
	}

	$url = trim( $url );

	$pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/\n\s]+/\S+/|(?:v|e(?:mbed)?|shorts|live)/|\S*?[?&]v=)|youtu\.be/)([a-zA-Z0-9_-]{11})%i';

	if ( preg_match( $pattern, $url, $matches ) ) {
		return $matches[1];
	}

	if ( preg_match( '/^[a-zA-Z0-9_-]{11}$/', $url ) ) {
		return $url;
	}

	return '';
}

/**
 * Build a privacy-friendly youtube-nocookie embed URL.
 *
 * @param string $video_id   11-char video ID.
 * @param bool   $related    Show related videos at the end.
 * @param int    $start_time Start time in seconds.
 * @return string
 */
function mtdevpvb_build_src( $video_id, $related = false, $start_time = 0 ) {
	$query = array(
		'rel'            => $related ? '1' : '0',
		'modestbranding' => '1',
	);
	if ( $start_time > 0 ) {
		$query['start'] = (int) $start_time;
	}

	return add_query_arg(
		$query,
		'https://www.youtube-nocookie.com/embed/' . rawurlencode( $video_id )
	);
}

/**
 * Rewrite native YouTube oEmbeds to the no-cookie domain (also on cached HTML).
 *
 * @param string $html oEmbed HTML.
 * @param string $url  Embedded URL.
 * @return string
 */
function mtdevpvb_rewrite_oembed_html( $html, $url ) {
	if ( false === stripos( $url, 'youtube.com' ) && false === stripos( $url, 'youtu.be' ) ) {
		return $html;
	}

	return str_ireplace(
		array( '://www.youtube.com/embed', '://youtube.com/embed' ),
		'://www.youtube-nocookie.com/embed',
		$html
	);
}
add_filter( 'embed_oembed_html', 'mtdevpvb_rewrite_oembed_html', 10, 2 );
add_filter(
	'oembed_dataparse',
	function ( $return, $data, $url ) {
		return mtdevpvb_rewrite_oembed_html( $return, $url );
	},
	10,
	3
);

/**
 * Clear the stored oEmbed cache so legacy embeds regenerate through the filter.
 *
 * @return void
 */
function mtdevpvb_clear_oembed_cache() {
	global $wpdb;

	$wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '\_oembed\_%'"
	);

	delete_expired_transients( true );
}
register_activation_hook( __FILE__, 'mtdevpvb_clear_oembed_cache' );

/**
 * Admin-bar shortcut to clear the embed cache on demand.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
 * @return void
 */
function mtdevpvb_admin_bar( $wp_admin_bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$wp_admin_bar->add_node(
		array(
			'id'    => 'mtdevpvb-clear-cache',
			'title' => __( 'Clear YouTube embed cache', 'mtdev-privacy-video-block' ),
			'href'  => wp_nonce_url( admin_url( 'admin-post.php?action=mtdevpvb_clear_cache' ), 'mtdevpvb_clear_cache' ),
		)
	);
}
add_action( 'admin_bar_menu', 'mtdevpvb_admin_bar', 100 );

/**
 * Handle the cache-clear action.
 *
 * @return void
 */
function mtdevpvb_handle_clear_cache() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'mtdev-privacy-video-block' ) );
	}
	check_admin_referer( 'mtdevpvb_clear_cache' );
	mtdevpvb_clear_oembed_cache();
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_mtdevpvb_clear_cache', 'mtdevpvb_handle_clear_cache' );
