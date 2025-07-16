# mtdev-privacy-video-block
A privacy-first WordPress plugin to load video embeds on consent, improving privacy, accessibility, and performance.
[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/your-plugin-slug?style=flat-square)](https://wordpress.org/plugins/your-plugin-slug/)
[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL%20v2%20or%20later-blue.svg?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)

A privacy-first WordPress plugin to load video embeds on consent, improving site performance, accessibility, and GDPR compliance.

## 🧐 Why Use This Plugin?

Embedding videos from platforms like YouTube or Vimeo directly on your site can introduce several issues:
* **Privacy Concerns:** They load tracking cookies before the user has given consent, which can be a problem under regulations like GDPR.
* **Performance:** Loading the full video player, even when it's not played, adds significant weight to your page, slowing it down.
* **Accessibility:** Default embeds often lack proper accessibility attributes.

## ✨ What It Does

**MTDEV Privacy Video Block** automatically detects video embeds on your pages and replaces them with a lightweight, accessible placeholder. The actual video is only loaded if the user explicitly clicks on the placeholder, ensuring that tracking scripts are not loaded without consent.

## 🚀 Key Features

* **Privacy by Design:** No external requests or cookies until the user agrees. Helps with GDPR and ePrivacy compliance.
* **Performance Boost:** Drastically reduces initial page load time and weight by deferring video player loading.
* **Accessibility Ready:** The placeholder is designed with accessibility in mind, providing a better experience for all users.
* **Highly Customizable:** (You can add this later) Control the appearance of the placeholder via the Customizer.
* **Zero Configuration:** Works out of the box. Just install and activate.
* **Block Editor & Shortcode Support:** (You can add this later) Easily add privacy-enhanced videos using a dedicated block or a simple shortcode.

## 🛠️ Installation

1.  Download the plugin from the [WordPress Plugin Repository](https://wordpress.org/plugins/your-plugin-slug/) (link will work once it's approved) or upload the entire `mtdev-privacy-video-block` folder to the `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  That's it! The plugin will automatically start managing your existing and new video embeds.

## 📄 License

This plugin is licensed under the GPL v2 or later.

---
*A project by MTDEV.*