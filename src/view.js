/**
 * Click-to-load: inject the YouTube iframe only when the visitor clicks play.
 */
( function () {
	'use strict';

	function loadPlayer( button ) {
		const frame = button.closest( '.mtdevpvb-frame' );
		const id = button.getAttribute( 'data-id' );
		if ( ! frame || ! id ) {
			return;
		}

		const params = button.getAttribute( 'data-params' ) || '';
		const src =
			'https://www.youtube-nocookie.com/embed/' +
			encodeURIComponent( id ) +
			'?autoplay=1' +
			( params ? '&' + params : '' );

		const iframe = document.createElement( 'iframe' );
		iframe.src = src;
		iframe.title = button.getAttribute( 'data-title' ) || 'YouTube video player';
		iframe.setAttribute( 'frameborder', '0' );
		iframe.setAttribute( 'loading', 'lazy' );
		// Required by YouTube since late 2025, otherwise playback fails (Error 153).
		iframe.setAttribute( 'referrerpolicy', 'strict-origin-when-cross-origin' );
		iframe.setAttribute(
			'allow',
			'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
		);
		iframe.setAttribute( 'allowfullscreen', '' );

		frame.innerHTML = '';
		frame.appendChild( iframe );
		iframe.focus();
	}

	document.addEventListener( 'click', function ( event ) {
		const button = event.target.closest( '.mtdevpvb-play' );
		if ( button ) {
			event.preventDefault();
			loadPlayer( button );
		}
	} );
}() );
