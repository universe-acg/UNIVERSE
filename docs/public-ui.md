# Public page polish

Universe retains its original blue-and-white identity and typefaces. The public shell shares a 1,280px content width, accessible controls, responsive catalogue grids, bounded media, and consistent page headings and footers. Dashboard styling remains independent.

## Behavior repaired

- Six catalogue pages share filters and cards. Narrow screens expose filters with an accessible toggle; desktop filters stay in normal document flow.
- News renders each record once. Listings uses actual records rather than twenty artificial copies. Keyword, category where supported, sorting, Clear links, and pagination stay on the correct route.
- Creator activity tabs work without Google Maps, expose one panel at a time, support keyboard navigation, and retain their URL hash.
- Event badges use ICU formatting correctly. Details, comments, similar events, venue galleries, and facility booking layouts fit narrow screens.
- Booking date/time choices reflect the facility schedule. Unavailable durations are disabled, changing duration refreshes times, and guest visitors receive a sign-in action. Server-side availability and overlap validation remain authoritative.
- Registration and sign-in use natural page height, visible labels, error messages, and password visibility controls.
- Landing statistics come from database totals. Images are illustrative for demo events. About, Privacy, Cookies, and Terms describe this installation rather than repeating a placeholder paragraph.
- Help Center categories, search, and articles retain the existing content and routes. Contact uses configured contact channels and links to twelve featured answers.

## Safe deployment updates

`app:demo:repair-images` restores bundled media and upgrades demo-owned placeholder references. It preserves custom uploads and does not recreate accounts or interactions. Repeated runs report zero reference changes after repair.

`app:pages:sync` replaces only registered demo placeholder content or previously installed versions. It preserves administrator content/title changes. Terms remains at `/page/legal-notice`; title changes cannot rename established page slugs.

Community fixture versioning now separates data changes from asset-maintenance code. A recognized legacy version is advanced without reapplying user-editable community records.

This UI change adds no schema migration. Existing index migrations and their entity mappings are retained from the preceding release.

## Verification

Run the standard PHP/Twig/container/YAML/data checks and production asset build. `PublicUiTest` covers page-update ownership, content destinations, combined filtering/sorting, ICU labels/timezone, and local media fallbacks.

The read-only browser smoke test checks the requested public pages and representative details at 375, 768, 1,024, 1,440, and 1,920px:

```bash
UNIVERSE_BROWSER=/path/to/chromium TMPDIR=/var/tmp python tests/browser/public_ui.py \
  --base-url http://127.0.0.1:18301 --output /var/tmp/universe-public-check
```

Test registration success/failure, authenticated booking and overlap rejection, filters, pagination uniqueness, profile panels, and mobile menu/focus on isolated QA. Do not run mutation probes against production or expose demo passwords in logs.
