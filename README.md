# jr-plays-theme

Custom Timber/Twig WordPress theme for JR Plays. Dark creator-forward aesthetic, CSS Grid layout, Vite + Dart Sass build pipeline.

## Build

```bash
npm install
npm run build:assets    # production build → assets/dist/
npm run dev:assets      # watch mode
```

## WordPress Options

The theme reads the following `wp_options` values at runtime.

### `jr_featured_playlist_id`

YouTube playlist ID embedded as an iframe in the homepage sidebar.

```bash
wp option update jr_featured_playlist_id "PLxxxxxxxxxxxx" --allow-root
```

**To show your latest uploads without creating or maintaining a playlist manually:**

Every YouTube channel has a built-in uploads playlist that always contains all your videos newest-first. To get its ID:

1. Go to **YouTube Studio → Settings → Channel → Advanced settings**
2. Copy your **Channel ID** (starts with `UC…`)
3. Replace the `UC` prefix with `UU` — that's your uploads playlist ID

```
Channel ID:  UCabc123xyz
Playlist ID: UUabc123xyz
```

```bash
wp option update jr_featured_playlist_id "UUabc123xyz" --allow-root
```

This playlist updates automatically every time you upload a video — no manual maintenance needed.

> The sidebar playlist section is hidden on the homepage if this option is empty.

---

### `jr_playlists`

Ordered list of YouTube playlists displayed as a horizontal scrolling row at the bottom of the homepage. Always visible — shows a placeholder when empty.

Stored as a JSON array. Each entry needs an `id` (YouTube playlist ID) and an optional `label`.

```bash
wp option update jr_playlists '[
  {"id":"PLxxxxxxxxxxxx","label":"Let'\''s Plays"},
  {"id":"UUabc123xyz","label":"All Videos"}
]' --allow-root
```

To add a playlist, fetch the current value, append your entry, and update:

```bash
# View current list
wp option get jr_playlists --allow-root

# Replace the whole list (easiest from a local .json file)
wp option update jr_playlists "$(cat playlists.json)" --allow-root
```

**Uploads playlist tip:** Use `UU` + your channel ID (replace `UC` prefix) to get the auto-updating latest-videos playlist — no manual curation needed (see `jr_featured_playlist_id` above for the full instructions).

> Playlists render as 400 × 225 px iframes. YouTube's built-in playlist panel is visible at that width, showing the video list on the right side of the player.

## Templates

All templates are Twig files under `templates/`. They extend `templates/base.twig`.

| Template | Route |
|---|---|
| `front-page.twig` | Static front page / homepage |
| `archive.twig` | Post/review archive |
| `single.twig` | Single post or review |
| `page.twig` | Static pages |
| `search.twig` | Search results |
| `404.twig` | Not found |
| `partials/header.twig` | Site header + nav |
| `partials/footer.twig` | Site footer |
| `partials/post-card.twig` | Reusable post card (used in archive + homepage) |
| `partials/game-row.twig` | Game taxonomy row (sidebar) |

## SCSS Structure

```
assets/scss/
├── main.scss         # entry point — imports everything
├── _variables.scss   # colors, fonts, breakpoints
├── _reset.scss
├── _typography.scss
├── _layout.scss      # CSS Grid, sticky footer
├── _nav.scss
├── _hero.scss
├── _home.scss        # featured article, post card list
├── _playlists.scss   # homepage bottom playlist row
├── _sidebar.scss
├── _single.scss
└── _archive.scss
```
