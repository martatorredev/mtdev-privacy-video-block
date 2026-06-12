=== MTDev Privacy Video Block ===
Contributors: martatorre
Tags: youtube, video, embed, privacy, gdpr, nocookie, gutenberg, block, accessibility
Requires at least: 6.3
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embed YouTube videos without tracking cookies using youtube-nocookie.com. Privacy-first, GDPR/CCPA and WCAG 2.2 friendly.

== Description ==

MTDev Privacy Video Block adds a Gutenberg block that embeds YouTube videos through the privacy-enhanced **youtube-nocookie.com** domain. No tracking cookies are set until a visitor actually presses play, which helps with GDPR and CCPA compliance.

It also transparently rewrites existing/legacy YouTube embeds (core Embed block or bare URLs) so the whole site benefits.

= Features =

* Embeds YouTube videos using `youtube-nocookie.com` automatically.
* Fully integrated with the WordPress block editor (Gutenberg).
* Customizable accessibility title, caption, aspect ratio and maximum width.
* Alignment options (left, center, right, wide, full) directly from the editor.
* Lightweight and optimized: lazy-loaded iframes, no front-end JavaScript.
* GDPR/CCPA friendly.
* Built with accessibility in mind (WCAG 2.2 — see below).
* Automatically rewrites old core YouTube embeds to `youtube-nocookie.com` and can clear the oEmbed cache on demand.

= Accessibility (WCAG 2.2) =

* The embedded iframe always has a meaningful title (1.1.1, 2.4.1, 4.1.2).
* The URL field has a programmatic label; validation errors are announced via a live region (1.3.1, 3.3.2, 4.1.3).
* Captions are associated with the figure and never rendered with reduced opacity, preserving contrast (1.3.1, 1.4.3).
* Interactive editor targets meet the minimum target size (2.5.8).
* No autoplay, so there is no moving content to pause (2.2.2).

== Installation ==

1. Upload the `mtdev-privacy-video-block` folder to `/wp-content/plugins/`, or install the ZIP via Plugins → Add New → Upload Plugin.
2. Activate the plugin.
3. Add the **YouTube (Privacy)** block and paste a YouTube URL.

To force-refresh previously cached embeds, use the "Clear YouTube embed cache" item in the admin bar.

== Frequently Asked Questions ==

= Does this make YouTube fully cookieless? =
The `youtube-nocookie.com` domain prevents tracking cookies on initial load. YouTube may still set cookies once the visitor presses play. For strict consent flows, pair it with a consent banner.

= Does it affect existing videos? =
Yes. Core YouTube oEmbeds are served from the no-cookie domain too. Clearing the cache regenerates stored markup.

== Changelog ==

= 1.0.0 =
* Initial release.
