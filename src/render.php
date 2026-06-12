<?php
/**
 * Server-side render template (privacy-first, click-to-load).
 *
 * No YouTube iframe — and no YouTube thumbnail — is output on page load.
 * A neutral play button is rendered instead; the iframe is injected by
 * view.js only when the visitor clicks. This means zero contact with Google
 * until the user opts in (strong GDPR/CCPA posture) and no Error 153 anywhere.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package MTDev\PrivacyVideoBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

$mtdevpvb_video_id = isset( $attributes['videoId'] ) ? sanitize_text_field( $attributes['videoId'] ) : '';

if ( '' === $mtdevpvb_video_id && ! empty( $attributes['url'] ) && function_exists( 'mtdevpvb_extract_video_id' ) ) {
	$mtdevpvb_video_id = mtdevpvb_extract_video_id( $attributes['url'] );
}

if ( '' === $mtdevpvb_video_id ) {
	return '';
}

$mtdevpvb_title = ( isset( $attributes['title'] ) && '' !== $attributes['title'] )
	? sanitize_text_field( $attributes['title'] )
	/* translators: iframe title fallback for screen readers. */
	: __( 'YouTube video player', 'mtdev-privacy-video-block' );

$mtdevpvb_caption = isset( $attributes['caption'] ) ? $attributes['caption'] : '';
$mtdevpvb_aspect  = isset( $attributes['aspectRatio'] ) ? $attributes['aspectRatio'] : '16/9';
$mtdevpvb_start   = isset( $attributes['startTime'] ) ? absint( $attributes['startTime'] ) : 0;
$mtdevpvb_related = ! empty( $attributes['relatedVideos'] );
$mtdevpvb_max     = isset( $attributes['maxWidth'] ) ? absint( $attributes['maxWidth'] ) : 0;

// Embed query params (added to the URL by view.js, together with autoplay=1).
$mtdevpvb_query = array(
	'rel'            => $mtdevpvb_related ? '1' : '0',
	'modestbranding' => '1',
);
if ( $mtdevpvb_start > 0 ) {
	$mtdevpvb_query['start'] = $mtdevpvb_start;
}
$mtdevpvb_params = http_build_query( $mtdevpvb_query );

// Aspect-ratio padding fallback (% = height / width * 100).
$mtdevpvb_ratios  = array(
	'16/9' => 56.25,
	'4/3'  => 75,
	'1/1'  => 100,
	'9/16' => 177.78,
	'21/9' => 42.857,
);
$mtdevpvb_padding = isset( $mtdevpvb_ratios[ $mtdevpvb_aspect ] ) ? $mtdevpvb_ratios[ $mtdevpvb_aspect ] : 56.25;

$mtdevpvb_style   = $mtdevpvb_max > 0 ? sprintf( 'max-width:%dpx;', $mtdevpvb_max ) : '';
$mtdevpvb_wrapper = get_block_wrapper_attributes( array( 'style' => $mtdevpvb_style ) );
$mtdevpvb_uid     = wp_unique_id( 'mtdevpvb-' );

// Visible texts (translatable, rendered server-side).
$mtdevpvb_label = __( 'Load video', 'mtdev-privacy-video-block' );
$mtdevpvb_note  = __( 'Playing loads content from YouTube, which may set cookies.', 'mtdev-privacy-video-block' );
/* translators: %s: video title. */
$mtdevpvb_aria  = sprintf( __( 'Play video: %s', 'mtdev-privacy-video-block' ), $mtdevpvb_title );
$mtdevpvb_watch = 'https://www.youtube.com/watch?v=' . rawurlencode( $mtdevpvb_video_id );

// Neutral play button (no external assets, no Google request).
$mtdevpvb_button = sprintf(
	'<button type="button" class="mtdevpvb-play" data-id="%1$s" data-params="%2$s" data-title="%3$s" aria-label="%4$s">' .
		'<span class="mtdevpvb-play-icon" aria-hidden="true">' .
			'<svg viewBox="0 0 24 24" focusable="false"><path d="M8 5v14l11-7z"/></svg>' .
		'</span>' .
		'<span class="mtdevpvb-play-text">%5$s</span>' .
		'<span class="mtdevpvb-play-note">%6$s</span>' .
	'</button>',
	esc_attr( $mtdevpvb_video_id ),
	esc_attr( $mtdevpvb_params ),
	esc_attr( $mtdevpvb_title ),
	esc_attr( $mtdevpvb_aria ),
	esc_html( $mtdevpvb_label ),
	esc_html( $mtdevpvb_note )
);

// No-JS fallback: a plain link to YouTube (no tracking iframe).
$mtdevpvb_noscript = sprintf(
	'<noscript><a class="mtdevpvb-fallback" href="%1$s" target="_blank" rel="noopener">%2$s</a></noscript>',
	esc_url( $mtdevpvb_watch ),
	esc_html__( 'Watch video on YouTube', 'mtdev-privacy-video-block' )
);

$mtdevpvb_frame = sprintf(
	'<div class="mtdevpvb-frame" style="aspect-ratio:%1$s;padding-bottom:%2$s%%;">%3$s%4$s</div>',
	esc_attr( str_replace( '/', ' / ', $mtdevpvb_aspect ) ),
	esc_attr( $mtdevpvb_padding ),
	$mtdevpvb_button,
	$mtdevpvb_noscript
);

$mtdevpvb_figcaption = '';
if ( '' !== trim( wp_strip_all_tags( $mtdevpvb_caption ) ) ) {
	$mtdevpvb_figcaption = sprintf(
		'<figcaption id="%1$s" class="mtdevpvb-caption">%2$s</figcaption>',
		esc_attr( $mtdevpvb_uid . '-cap' ),
		wp_kses(
			$mtdevpvb_caption,
			array(
				'a'      => array(
					'href'   => array(),
					'rel'    => array(),
					'target' => array(),
				),
				'strong' => array(),
				'em'     => array(),
				'b'      => array(),
				'i'      => array(),
				'br'     => array(),
				'code'   => array(),
			)
		)
	);
}

$mtdevpvb_label_by = '' !== $mtdevpvb_figcaption
	? sprintf( ' aria-labelledby="%s"', esc_attr( $mtdevpvb_uid . '-cap' ) )
	: '';

printf(
	'<figure %1$s%2$s>%3$s%4$s</figure>',
	$mtdevpvb_wrapper,     // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$mtdevpvb_label_by,    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$mtdevpvb_frame,       // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$mtdevpvb_figcaption   // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
