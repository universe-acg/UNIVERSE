# Public UI release, verified 2026-11-05 21:42 UTC

This section supersedes the historical release status below. The existing Universe identity is preserved; the requested public-page polish is implemented, pushed and live.

- Active application commit: **`82cf095`**, Forge deployment **`82244592`**, finished successfully. Main implementation: `9e384e7`; final News thumbnail repair: `82cf095`.
- The active production symlink points to `82cf095`; `/health` passes. Existing index work through `bb03821` was retained. This UI change adds no migration.
- Polished landing, Events/Creators/Venues/Athletics/Listings/News catalogues and details, creator activity/member tabs, Contact, Help Center, About/Privacy/Cookies/Terms, registration, confirmation and sign-in. Shared responsive grids, bounded media, accessible controls, header/footer spacing and image fallbacks are in place.
- Removed duplicate News rendering and twenty-fold Listings duplication. Venue/facility/opportunity filters, sorting, Clear links, pagination and creator panels work. Calendar formatting uses ICU correctly. Booking choices reflect schedules and refresh when duration changes.
- Landing totals are server-rendered counts. Four information pages now have distinct factual demo content. Terms retains `/page/legal-notice`. Existing Help Center answers and URLs remain intact.
- Deployment ran `app:demo:repair-images` and `app:pages:sync`, upgrading 1,373 demo placeholder references and four information pages. Repeated production runs report **zero** reference/content updates. No full production reseed occurred.
- Media repair preserves custom uploads. Page synchronization preserves administrator content/title edits and avoids title-driven slug renaming. Community data versioning recognizes both prior asset-only revisions without reapplying edited fixtures.
- Scoped production backup: `/home/forge/universe-public-ui-backup-20261105/`, outside the web root. Contains affected media/page metadata and original placeholder images, no public artifact.

Verification:

- **22 PHPUnit tests / 493 assertions** pass. Container, all **225 Twig templates**, relevant YAML, JSON data and standalone JS checks pass. Production asset build succeeds with existing legacy Sass deprecation warnings.
- Final production browser crawl: **17 public pages plus five representative detail pages**, each at **375/768/1024/1440/1920px**. No horizontal overflow, JavaScript errors, or failed image/script/style/media requests. One H1 per page/detail.
- QA: keyword/category/sort/Clear behavior, unique News/Listings pagination, correct creator panels/hash/keyboard controls, mobile menu focus/Escape, filter toggle, attendee/creator validation and successful registration, successful booking and overlap rejection all pass.
- All **83 Help Center/contact/category/article pages**, search and no-results pass on QA. Existing Reels/Live/Leaderboards and three-role Chat regression checks pass on QA and production, with no runtime warnings or failed resources on final runs.
- Final News article image-cache failure was resolved by using local native thumbnails. Do not restore its old `imagine_filter` runtime URLs.
- Temporary deployment tokens were revoked and raw token/session/page captures removed. Demo account passwords remain private. No live messages, registrations or reservations were created by production testing.

Maintenance:

- Design context: `.impeccable.md`. Behavior/commands: `docs/public-ui.md`.
- Reproducible read-only browser smoke: `tests/browser/public_ui.py --base-url URL --output /var/tmp/OUTPUT`, with `UNIVERSE_BROWSER` pointing to Chromium.
- Temporary QA interaction scripts/reports: `/var/tmp/universe-polish-flows*`, `/var/tmp/universe-polish-account-flows*`, `/var/tmp/universe-polish-final-qa*`. Production report/screenshots: `/var/tmp/universe-polish-production/`; community regression: `/var/tmp/universe-polish-production-community.log`.
- Browser checks use Chromium viewport emulation. Physical-device/Safari testing was not performed.
- The following documentation commit does not require another application deployment. Both working copies and Git metadata are synchronized at handoff.

---

# UNIVERSE handoff, 2026-11-05

This is the continuation record for Claude. The user requested **UC** so today's work, current deployment and next steps are explicit. Do not restart modernization or redesign the UI.

## Immediate status, verified 2026-11-05 09:17 UTC

- Latest functional code: **`6880d46`**, pushed to `master` and deployed successfully as Forge release **`82170163`**.
- The actual production `current` commit is `6880d46`. Health and Doctrine schema validation pass; the corrected Reels and comments templates have no ad hoc source differences.
- Final production browser checks passed for Help Center, FAQ, Reels/gallery/detail, Live/list/player, Leaderboards, and Chat under administrator/student/organizer accounts on desktop/mobile.
- Final results: **no JavaScript errors, console warnings, missing resources or horizontal overflow**. A separate post-activation resource crawl reported no failed requests. `/js/comments.js` returns 200.
- Earlier temporary live repairs were superseded by this replacement release. Do not restore or redeploy `cf2685a`.
- The temporary deployment API token was revoked and 11 private token/cookie/page captures were removed. Private demo credential files remain available to the owner.
- The following repository commit contains this documentation handoff only. It does not need another application deployment.
- No known blocker remains in the requested Help Center/community-page scope. `TODO.md` separates completed work from optional maintenance.

## What the user asked

The original request was to recover an old/broken Universe project, modernize its dependencies, research Deree deeply, populate a full realistic demo and deploy it properly on the existing Forge server. The user objected to UI changes and explicitly asked to retain the original design. They authorized pushing, site creation, server access and deployment, and asked not to be repeatedly prompted for permission. A Google Calendar complaint was explicitly withdrawn.

Today's follow-ups covered missing header/navigation links and the menu editor, then full FAQ/Help Center content, then functionality and population of `/reels`, `/lives`, `/leaderboards` and especially `/chat`.

## Production map

| Item | Value |
| --- | --- |
| Public site | https://universe-deree-live-3b97bb.on-forge.com |
| Forge organization | `alexandros-kourtis` |
| Forge server | `helppoint-production`, ID `950296` |
| Forge site | `3483627` |
| Forge UI | https://forge.laravel.com/alexandros-kourtis/helppoint-production/3483627 |
| SSH | `forge@64.227.66.233` |
| Site root | `/home/forge/universe-deree-live-3b97bb.on-forge.com` |
| Active app | Site root + `/current` |
| Production DB | `universe_demo_20261105` |
| Runtime | Ubuntu 24.04, PHP 8.2, MySQL 8.0 |
| Source | Public GitHub repository `universe-acg/UNIVERSE`, branch `master` |

The site uses Forge releases. Shared paths include `.env.local`, `var/log`, `var/private`, `var/sessions`, `public/uploads`, and `var/demo-credentials.json`. Keep versioned `var/.gitignore`, because the parent directory must exist before Forge's shared-path setup. Do not repeat the previous attempt to create a duplicate Forge site; the obsolete initial site was already removed.

Forge deployment structure:

```bash
set -e
$CREATE_RELEASE()
cd $FORGE_RELEASE_DIRECTORY
export FORGE_RELEASE_DIRECTORY
export FORGE_SITE_ROOT=/home/forge/universe-deree-live-3b97bb.on-forge.com
export FORGE_SITE_PATH="$FORGE_RELEASE_DIRECTORY"
export APP_URL=https://universe-deree-live-3b97bb.on-forge.com
bash deploy/forge.sh
$ACTIVATE_RELEASE()
curl --fail --silent --show-error https://universe-deree-live-3b97bb.on-forge.com/health
```

`deploy/forge.sh` installs locked dependencies, builds assets, validates research JSON and Twig, migrates, runs scoped seed updates, clears/warms cache and validates schema. A push does not automatically trigger deployment. Forge shared symlinks and installed assets produce expected working-tree differences (`.env`, `public/uploads`, generated bundle assets); judge a release by its commit, intended sources and HTTP checks, not an entirely empty remote `git status`.

## Earlier work completed today

- Upgraded the old application to Symfony 7.4 / Doctrine ORM 3 / DBAL 4, PHP 8.2 compatibility, and current locked frontend build dependencies with Node 22.
- Restored entity mappings, migrations and native compatibility replacements for the retired user/comments integrations.
- Added role/ownership checks, CSRF-protected POST mutations, secure document access and captured demo delivery.
- Restored sign-in and registration, fixed local authentication artwork, full current timezone data and calendar interaction loading. The old EqualWeb key is disabled unless a valid domain-specific key is configured.
- Preserved sessions and private files across releases.
- Installed working scheduled event publication and daily idempotent reminder processing.
- Researched official ACG directories and event history. The directory contains **63 clubs, 20 societies, 10 organizations, 28 venues and six facilities**. `event_facts.json` contains **1,376 research rows**. Research records retain source URLs and verification metadata.
- Established an isolated full demo covering the mapped entities, with fictional accounts/interactions/documents and researched public organization/event facts. Current baseline has **596 accounts**, including 500 fictional student accounts and researched creator profiles plus an archive caretaker.
- Earlier Composer/npm/GitHub advisory checks reported zero open dependency advisories. Those audit results belong to the earlier checks, do not describe them as a new audit without rerunning.

Operational schedules already configured in Forge:

- Job `2206639`: nonoverlapping minute Messenger consumption for scheduled event publication.
- Job `2206640`: hourly reminder command; delivers only at 08:00 Europe/Athens and suppresses duplicate reminders.

Do not touch other applications' processes or schedules on this shared server.

## Navigation and menus

Commit `81c75ce` restored navigation using the existing styling:

- Header has 15 configured entries; the main campus sections and More dropdown are visible to the correct roles.
- Footer menus contain 6 / 8 / 9 entries with persisted headings.
- Added menu audience/placement fields and safe URL validation, repaired obsolete links and fixed organizer dashboard access.
- Fixed heading persistence, addition/removal/reordering, including a race where saving immediately after Remove retained the row.
- Restored mobile dashboard navigation, keyboard close/focus and notifications.
- Added `app:menus:sync`; its version marker prevents ordinary redeploys from overwriting administrator changes.
- Verified 99 navigation checks, four account roles, four viewport widths, all four editor forms, and production schema/health.

See `docs/navigation.md`.

## FAQ and Help Center, deployed

Commit `64cbead`, deployed in release **`82162309`**:

- Added **72 full answers in nine categories**, with **12 featured FAQs** reused on `/contact`.
- Content covers getting started, accounts/privacy, events/tickets, clubs, organizer tools, athletics/venues, careers/volunteering, campus services and technical help.
- Campus-service information links to verified official ACG pages. Demo actions are distinguished from real College services; no passwords are embedded in articles.
- Replaced the 255-character article storage limit with text storage, allowed reusable tags, repaired sorting/search, preserved legacy article URLs and improved administrator editing.
- `data/deree/help-center.json` is shared by full seeding and `app:help-center:seed`.
- Scoped seeding preserves unrelated administrator content, existing views and later edits on unchanged catalogue versions.
- Verified all **83 public Help Center/category/article/FAQ pages**, search/no-results, long-content create/edit, reusable tags, URL stability and desktop/mobile rendering. Production checks passed.

See `docs/help-center.md`.

## Community pages and Chat

Main commit `c122bac`, deployed in release **`82167166`**, followed by badge fixes `36cb549` and `0012551` (release `82167750`).

### Reels

- Removed artificial 20x duplication and random reshuffling.
- Deterministic database pagination of 24 records; real page counts; functional search; no duplicate Load More records.
- Working selected-image/video dialog, keyboard controls, focus return and detail route.
- Working idempotent like/unlike and threaded comments. Personal legacy like APIs now restrict access and expose limited public data.
- Hidden/deleted media is excluded from public views and searches.
- Repaired image and MP4/WebM uploads, safe unique naming, caption storage up to 1,000 characters and owner checks.
- Added approved local demo media, preserving the existing grid/dialog styling.

### Live

- Functional organizer management: create/edit, owner checks, cover, stream URL, preview/upcoming/live/ended state, hide/show, logical delete and restore.
- Corrected the management template that incorrectly rendered athletic facilities.
- Expanded titles/URLs and added owner mapping.
- YouTube, Vimeo and Twitch resolve to approved player origins. Twitch uses the current production hostname as parent.
- Four seeded sessions contain a playable original local WebM **preview**, clearly labelled rather than fabricated College live broadcasts.
- Other valid HTTPS links stay explicit external links; arbitrary iframe HTML is not accepted.

### Leaderboards

- Selects actual enabled/undeleted attendees and organizers; administrators are excluded from attendee rankings.
- Stable score/username sorting; creators no longer disappear solely because their scores are low.
- Functional tabs, text and level filters, deep-linked tags, reset and empty results.
- Demonstration points populate all student tiers and varied organizer tiers.
- Participation badges now use local URLs. `scripts/install_badges.php` installs the original bundled artwork into shared uploads, including a bundled special-badge fallback. No new visual redesign was introduced.

### Chat

- Start direct conversations by username or profile link; atomic participant-pair keys prevent duplicate direct rooms.
- Existing seeded group rooms stay separate; membership checks cover room HTML, history and writes.
- Plain escaped Unicode text, 4,000-character limit and CSRF-protected writes.
- Per-client message IDs make ambiguous send retries idempotent.
- Visible-tab polling every three seconds; no cursor gaps or duplicate rows; draft preservation on failure; connection/send status.
- Latest 200 messages plus older-history loading with chronological order and scroll preservation.
- Persistent per-member read cursors and mute state; mobile conversation list and start form.
- Leaving a direct room clears its pair key, preventing a later start from silently rejoining that old history.
- Sender-only legacy edit/delete APIs; old send endpoints now share the same checks and rate limit.
- Rate limit is 60 send requests per user per minute.
- Optional WebSocket remains loopback-only; the deployed UI does not require it.

### Community fixtures and full seeding

`app:community:seed` populates/enriches demo-owned data without recreating accounts or deleting real activity:

- 60 distinct reels: 50 image posts, 10 video posts.
- Four playable Live previews.
- Five curated group conversations / 20 new messages, in addition to the original 30 rooms / 120 messages.
- Fictional varied scores for registered demo accounts whose scores were zero.

Sources are `data/deree/community-demo.json`, `CommunityDemoData`, and original illustrative assets in `data/demo-media`. Shared media files are retained on updates so changing one seeded record cannot delete the file used by another.

Three preexisting research JSON files contained unescaped newlines and prevented clean full seeding. Their encoding was repaired without changing the values. `scripts/validate_demo_data.php` validates all 11 JSON inputs during deployment.

A separate fresh database was seeded **twice** successfully with unchanged totals:

| Table/data | Count |
| --- | ---: |
| Accounts | 596 |
| Help articles | 72 |
| Reels | 60 |
| Live sessions | 4 |
| Conversations | 35 |
| Messages | 140 |
| Registered fixture records | 16,026 |

That temporary full-seed database and its grant were subsequently removed. Production was not fully reseeded.

See `docs/community.md`.

## Verification evidence

- Latest PHPUnit: **17 tests, 474 assertions**, passing.
- All **223 Twig templates**, container, relevant YAML, research JSON and standalone JS syntax checks passed on the corrected code.
- Doctrine schema validation passed on the isolated QA database and the deployed main release.
- Browser/API checks passed: two-user chat, Unicode/XSS rendering, retry after a simulated network drop following a successful save, CSRF, nonparticipant isolation, sender-only mutations, persistent read/mute state, 200+ message history, rate limits including the legacy send route.
- Reels browser checks passed: unique pagination, modal navigation/Escape, actual WebM playback, detail page, like/unlike. Real image and video uploads and comment posting passed on QA.
- Live CRUD, long title/URL, other-organizer denial, hide/show, deletion/restoration and playback passed on QA.
- Leaderboard population, role tabs, search, tier filters, reset and mobile containment passed on QA.
- Network checks on the corrected local-badge version reported **no failed resources or console warnings**.
- Reels viewport resizing exposed a legacy `transition: all` that animated the gallery width and briefly overflowed. It was removed, and the original small-screen column breakpoints restored. The corrected immediate resize check returned width=375, scrollWidth=375, no overflowing elements.
- **Important incident:** the first CSS edit formed `{#reel-modal`, which Twig interprets as an unclosed comment. `cf2685a` must not be redeployed. `aa04610` fixes the syntax and adds a deployment lint gate. Cache warmup and `/health` alone had not caught this page-specific problem.

The live resource check additionally caught the comments-script cleanup described above; keep custom compatibility assets outside generated bundle directories.

Final clean-release browser verification is recorded below; do not confuse the earlier failed resize run with a final success.

## Local/server access for continuation

- Fast copy: `/var/tmp/universe-modernization-20261105`; shared copy: `/home/alexandroskourtis/homelab/shared/newwork/UNIVERSE`.
- Isolated QA app: `/home/forge/universe-demo-staging/app`, DB `universe_nav_20261105`, HTTP `127.0.0.1:8301` on server, tunnel `http://127.0.0.1:18301` locally.
- QA PHP server runs with opcache and CLI opcache disabled. Keep its private environment pointed to the QA database, not production.
- Browser executable: `/home/alexandroskourtis/.cache/ms-playwright/chromium_headless_shell-1243/chrome-headless-shell-linux64/chrome-headless-shell`.
- Python Playwright is available. Use `TMPDIR=/var/tmp`.
- Private demo credentials: production `current/var/demo-credentials.json` and local `var/demo-credentials.json`. Test usernames include `demo-admin`, `student-001`, `student-002`, `organizer-001`. Read the password privately; **do not commit it here**.
- Deployment backups: `/home/forge/universe-navigation-backup-20261105/menus.sql` and `/home/forge/universe-community-backup-20261105.sql`. They are private operational backups, not Git artifacts.

Temporary local tooling, not part of the repository:

- `/var/tmp/universe-forge-session.py`: obtains the existing authorized Forge session from local Chrome/keyring without printing its secrets.
- `/var/tmp/universe-forge-client.py`: private session/Inertia page helper.
- `/var/tmp/universe-forge-api.py`: scoped API helper; reads the private raw token file.
- `/var/tmp/universe-navigation-deploy.py`: triggers deployment for the existing correct site; the latest response is saved privately in `/var/tmp/universe-forge-navigation-deployment.json`.
- `/var/tmp/universe-navigation-deploy-status.py`: reads that latest deployment ID and queries status.
- `/var/tmp/universe-help-forge-token.py`: creates a minimal temporary token named `UNIVERSE Help Center deployment 2026-11-05`, expiring Nov 6. If still present, revoke this exact token after work, preserving the user's own tokens.
- API base: `https://forge.laravel.com/api`; organization route parameter is slug `alexandros-kourtis`, not a numeric ID.
- Deployment POST/GET: `/orgs/alexandros-kourtis/servers/950296/sites/3483627/deployments[/{id}]`; logs use `/{id}/log`.
- A minimal deployment token cannot call `/api/user`; 403 there does not mean the token is invalid. `/api/orgs` works with `organization:view`.
- After token creation through `/profile/api`, fetch `/profile/api/created` to obtain its private token response. Never print that response.

Test/report helpers under `/var/tmp`:

- `universe-help-check.py` and its production report: 83 Help Center pages and search.
- `universe-chat-check.py`: API isolation/CSRF/idempotence tests.
- `universe-community-browser.py`: two-browser chat and community interactions on QA.
- `universe-community-api-check.py`: Live ownership/CRUD and message ownership.
- `universe-media-upload-check.py`: actual image/video uploads and comments.
- `universe-chat-history-browser.py`, `universe-chat-rate-check.py`.
- `universe-community-network-check.py`, `universe-reels-overflow.py`.
- `universe-community-production-browser.py`: final public pages, playback, gallery, leaderboard, mobile and three-role Chat checks.

Private QA cookie/token captures are removed at cleanup; reauthenticate before running scripts that expect those captures. Some QA scripts assume exactly 60 media fixtures, while QA now also contains test-created records. Remove only the specifically named QA probes or adapt those assertions before rerunning. Do not delete real production data to satisfy a test.

## Final verification record

Verified on the activated `6880d46` release at 2026-11-05 09:17 UTC:

- Forge deployment `82170163`: finished successfully.
- Site `/health`: `{"status":"ok","application":"UNIVERSE"}`.
- Doctrine mapping/database schema: synchronized.
- `/help-center`, `/contact`, `/reels`, `/reel/demo-reel-0`, `/lives`, `/live/demo-live-0`, `/leaderboards`: browser checks passed, including applicable interactions and mobile/desktop containment.
- `/chat`: populated and working under administrator, student and organizer demo accounts; mobile start form/conversation access confirmed.
- Production browser report: no page errors, console warnings or failed image/media/script/style resources.
- Additional post-activation resource crawl: `[]` failed requests.
- Participation badges serve the original artwork locally; comments compatibility script serves from `/js/comments.js` with HTTP 200.
- Temporary deployment token revoked; raw token, session/page captures and QA cookie/token file deleted. The helper scripts remain, but need a newly authorized session/token capture to deploy again. Their dated token-expiry settings need updating if used after Nov 6.

Earlier failures remain documented above so they are not repeated. Do not describe those obsolete reports as current failures, or delete records to make a QA fixture-count assertion pass.
