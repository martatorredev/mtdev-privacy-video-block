<?php
/**
 * Server-side render template.
 *
 * Available variables:
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused, dynamic block).
 * @var WP_Block $block      Block instance.
 *
 * @package MTDev\PrivacyVideoBlock
 */

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

$mtdevpvb_src = function_exists( 'mtdevpvb_build_src' )
	? mtdevpvb_build_src( $mtdevpvb_video_id, $mtdevpvb_related, $mtdevpvb_start )
	: 'https://www.youtube-nocookie.com/embed/' . rawurlencode( $mtdevpvb_video_id );

// Aspect-ratio padding fallback (% = height / width * 100).
$mtdevpvb_ratios  = array(
	'16/9' => 56.25,
	'4/3'  => 75,
	'1/1'  => 100,
	'9/16' => 177.78,
	'21/9' => 42.857,
);
$mtdevpvb_padding = isset( $mtdevpvb_ratios[ $mtdevpvb_aspect ] ) ? $mtdevpvb_ratios[ $mtdevpvb_aspect ] : 56.25;

$mtdevpvb_style = $mtdevpvb_max > 0 ? sprintf( 'max-width:%dpx;', $mtdevpvb_max ) : '';

$mtdevpvb_wrapper = get_block_wrapper_attributes(
	array(
		'style' => $mtdevpvb_style,
	)
);

// Unique id so the figcaption can label the figure (WCAG 1.3.1 / 4.1.2).
$mtdevpvb_uid = wp_unique_id( 'mtdevpvb-' );

$mtdevpvb_iframe = sprintf(
	'<div class="mtdevpvb-frame" style="aspect-ratio:%1$s;padding-bottom:%2$s%%;">' .
	'<iframe src="%3$s" title="%4$s" loading="lazy" frameborder="0" ' .
	'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" ' .
	'referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>',
	esc_attr( str_replace( '/', ' / ', $mtdevpvb_aspect ) ),
	esc_attr( $mtdevpvb_padding ),
	esc_url( $mtdevpvb_src ),
	esc_attr( $mtdevpvb_title )
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

// If there is a caption, associate it with the figure for assistive tech.
$mtdevpvb_aria = '' !== $mtdevpvb_figcaption
	? sprintf( ' aria-labelledby="%s"', esc_attr( $mtdevpvb_uid . '-cap' ) )
	: '';

printf(
	'<figure %1$s%2$s>%3$s%4$s</figure>',
	$mtdevpvb_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$mtdevpvb_aria, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$mtdevpvb_iframe, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$mtdevpvb_figcaption // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
