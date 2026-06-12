# MTDev Privacy Video Block

Gutenberg block for embedding YouTube videos **without tracking cookies**, via `youtube-nocookie.com`. Privacy-first, GDPR/CCPA and **WCAG 2.2** friendly.

## 🚀 Features

- ✅ Embeds via `youtube-nocookie.com` automatically.
- ✅ Native Gutenberg block (title, caption, aspect ratio, max width).
- ✅ Alignment (left, center, right, wide, full) from the editor.
- ✅ Lazy-loaded iframes, no front-end JS.
- ✅ GDPR/CCPA friendly + accessible (WCAG 2.2).
- ✅ Rewrites old core YouTube embeds to the no-cookie domain + on-demand cache clear.

## Repo layout & build philosophy

This repo follows the **source-only** convention:

- `main` contains **only source** (`src/`, the main PHP file, `package.json`, configs).
- `node_modules/` and `build/` are **git-ignored** — compiled output is a *release artifact*, not something you version.
- CI (`.github/workflows/release.yml`) builds on each `v*` tag and attaches a ready-to-install zip to the GitHub Release.
- Optional `deploy-wporg.yml` pushes the build to the WordPress.org SVN repo.

```
mtdev-privacy-video-block/
├── mtdev-privacy-video-block.php   # bootstrap: registers build/, oEmbed rewrite, cache tools
├── src/
│   ├── block.json                    # metadata (mtdevpvb/video)
│   ├── index.js  edit.js             # editor (JSX)
│   ├── style.scss  editor.scss
│   └── render.php                    # server-side render (dynamic block)
├── build/                            # generated — not committed
├── .github/workflows/                # release + wp.org deploy
├── package.json
└── readme.txt
```

## Develop

```bash
npm install
npm run build     # generates build/
npm run start     # watch mode
npm run lint:js   # eslint
npm run plugin-zip
```

## Naming convention

- Folder / slug / text-domain: `mtdev-privacy-video-block`
- Block name: `mtdevpvb/video` → wrapper class `wp-block-mtdevpvb-video`
- PHP functions/constants: `mtdevpvb_` / `MTDEVPVB_`
- CSS helper classes are scoped under the wrapper (`.mtdevpvb-frame`, `.mtdevpvb-caption`)

## Privacy note

`youtube-nocookie.com` avoids tracking cookies on initial load; YouTube may still set cookies after the visitor presses play. For strict consent, pair with a cookie banner.

## License

GPL-2.0-or-later.
