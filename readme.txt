=== MTDev Privacy Video Block ===
Contributors: martatorre
Tags: youtube, video, embed, privacy, nocookie
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Privacy-first YouTube block: nothing loads from Google until the visitor clicks play. GDPR/CCPA and WCAG 2.2 friendly.

== Description ==

MTDev Privacy Video Block adds a Gutenberg block that embeds YouTube videos through the privacy-enhanced **youtube-nocookie.com** domain.

By default the block uses a **click-to-load** approach: the page shows a neutral play button and makes **no request to YouTube or Google at all** — not even the thumbnail — until the visitor clicks play. Only then is the player loaded. This is the strongest posture for GDPR/CCPA, because no personal data leaves the page without the visitor's explicit action.

It also transparently rewrites existing/legacy YouTube embeds (the core Embed block or bare URLs) to the no-cookie domain, so the whole site benefits.

= Features =

* Embeds YouTube videos using `youtube-nocookie.com` automatically.
* Click-to-load: no contact with YouTube/Google until the visitor presses play (no iframe and no thumbnail on page load).
* Fully integrated with the WordPress block editor (Gutenberg).
* Customizable accessibility title, caption, aspect ratio, maximum width and start time.
* Alignment options (left, center, right, wide, full) directly from the editor.
* Lightweight: a tiny front-end script and no player loaded until it is needed.
* No-JavaScript fallback: a plain link to the video on YouTube.
* GDPR/CCPA friendly and built with accessibility in mind (WCAG 2.2).
* Rewrites old core YouTube embeds to `youtube-nocookie.com` and can clear the oEmbed cache on demand from the admin bar.

= Accessibility (WCAG 2.2) =

* The play control is a real button with a descriptive accessible name, and the loaded player iframe carries a meaningful title (1.1.1, 2.4.1, 4.1.2).
* Keyboard focus moves into the player once it loads.
* In the editor, the URL field has a programmatic label and validation errors are announced via a live region (1.3.1, 3.3.2, 4.1.3).
* Captions are associated with the figure and never rendered with reduced opacity, preserving contrast (1.3.1, 1.4.3).
* Interactive targets meet the minimum target size (2.5.8).
* Playback never starts on its own — only after the visitor activates the button (2.2.2).

== Installation ==

1. Upload the `mtdev-privacy-video-block` folder to `/wp-content/plugins/`, or install the ZIP via Plugins → Add New → Upload Plugin.
2. Activate the plugin.
3. Add the **YouTube (Privacy)** block and paste a YouTube URL.

To force-refresh previously cached embeds, use the "Clear YouTube embed cache" item in the admin bar.

== Frequently Asked Questions ==

= Why do I see a play button instead of the video? =
That is the privacy feature. The video is not loaded until the visitor clicks, so YouTube and Google receive nothing on page load. After the click, the player loads and starts playing.

= Does this make YouTube fully cookieless? =
The `youtube-nocookie.com` domain prevents tracking cookies on initial load, and click-to-load means no request is made until the visitor plays the video. YouTube may still set cookies once playback starts. For strict consent flows, pair it with a consent banner.

= Does it affect existing videos? =
Yes. Core YouTube oEmbeds are served from the no-cookie domain too. Clearing the cache regenerates stored markup.

= What happens without JavaScript? =
The block shows a plain link to watch the video on YouTube, so no tracking iframe is loaded.

== Changelog ==

= 1.0.0 =
* Initial release.
