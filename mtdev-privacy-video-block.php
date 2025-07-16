<?php

/**
 * Plugin Name:       MTDEV: Privacy Video Block
 * Plugin URI:        https://github.com/martatorredev/mtdev-privacy-video-block
 * Description:       A privacy-first WordPress plugin to load video embeds on consent, improving site performance, accessibility, and GDPR compliance.
 * Version:           1.0.0
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            Marta Torre
 * Author URI:        https://martatorre.dev/
 * Contributors:      martatorre
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mtdev-privacy-video-block
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue scripts and styles for the front end.
 */
function mtdev_pvb_enqueue_assets()
{
    // Enqueue our stylesheet.
    wp_enqueue_style(
        'mtdev-pvb-style',
        plugin_dir_url(__FILE__) . 'assets/css/mtdev-pvb-style.css',
        array(),
        '1.0.0'
    );

    // Enqueue our JavaScript file.
    wp_enqueue_script(
        'mtdev-pvb-script',
        plugin_dir_url(__FILE__) . 'assets/js/mtdev-pvb-script.js',
        array(), // Dependencies, like jQuery. Empty for now.
        '1.0.0',
        true // Load in footer.
    );
}
add_action('wp_enqueue_scripts', 'mtdev_pvb_enqueue_assets');


/**
 * Filters the post content to replace video iframes with a placeholder.
 *
 * @param string $content The post content.
 * @return string The modified post content.
 */
function mtdev_pvb_filter_content($content)
{
    // This is a simple regex to find YouTube and Vimeo iframes.
    // We will improve it later.
    $pattern = '/<iframe[^>]+src="https?:\/\/(www\.youtube\.com|player\.vimeo\.com)\/embed\/([^"]+)"[^>]*><\/iframe>/';

    // preg_replace_callback finds all matches and lets us process them with a function.
    $content = preg_replace_callback($pattern, function ($matches) {
        // The full iframe tag is in $matches[0].
        // We need to extract the src attribute value cleanly.
        preg_match('/src="([^"]+)"/', $matches[0], $src_match);
        $video_src = $src_match[1];

        // This is our placeholder.
        // We store the original video source in a data-attribute for our JS to use later.
        $placeholder = '<div class="mtdev-pvb-placeholder" data-video-src="' . esc_attr($video_src) . '">';
        $placeholder .= '<p>Video content is blocked for your privacy.</p>';
        $placeholder .= '<button>Load Video</button>';
        $placeholder .= '</div>';

        return $placeholder;
    }, $content);

    return $content;
}
add_filter('the_content', 'mtdev_pvb_filter_content');
