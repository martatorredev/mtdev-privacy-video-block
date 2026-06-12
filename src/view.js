/**
 * MTDev Privacy Video Block — front-end view script.
 *
 * Click-to-load: the YouTube iframe is built and injected only when the
 * visitor clicks the play button. Until then there is zero contact with
 * Google. The iframe is created with autoplay=1 so it starts right after
 * the click, and with a valid referrer policy (avoids YouTube Error 153).
 */
( function () {
	'use strict';

	function loadPlayer( button ) {
		var frame = button.closest( '.mtdevpvb-frame' );
		if ( ! frame ) {
			return;
		}

		var id     = button.getAttribute( 'data-id' );
		var params = button.getAttribute( 'data-params' ) || '';
		var title  = button.getAttribute( 'data-title' ) || 'YouTube video player';

		if ( ! id ) {
			return;
		}

		var src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( id ) +
			'?autoplay=1' + ( params ? '&' + params : '' );

		var iframe = document.createElement( 'iframe' );
		iframe.src = src;
		iframe.title = title;
		iframe.setAttribute( 'frameborder', '0' );
		iframe.setAttribute( 'loading', 'lazy' );
		iframe.setAttribute( 'referrerpolicy', 'strict-origin-when-cross-origin' );
		iframe.setAttribute(
			'allow',
			'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share'
		);
		iframe.setAttribute( 'allowfullscreen', '' );

		frame.innerHTML = '';
		frame.appendChild( iframe );

		// Move keyboard focus into the freshly loaded player.
		iframe.focus();
	}

	// Delegated click handler covers any number of blocks on the page.
	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.mtdevpvb-play' );
		if ( button ) {
			event.preventDefault();
			loadPlayer( button );
		}
	} );
}() );
