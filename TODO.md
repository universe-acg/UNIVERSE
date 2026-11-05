# Public UI polish, completed 2026-11-05

- [x] Preserve and integrate the previously deployed header, landing, imagery and index work.
- [x] Unify all requested public layouts, cards, media, forms, navigation and footer presentation.
- [x] Repair duplication, filter destinations/behavior, pagination, ICU labels, creator tabs and booking controls.
- [x] Complete distinct About/Privacy/Cookies/Terms content with safe scoped updates.
- [x] Verify registration and reservation success/error behavior on isolated QA.
- [x] Deploy final application `82cf095` through Forge deployment `82244592`.
- [x] Verify live public pages/details at five widths and existing community/three-role Chat behavior.
- [x] Confirm media/content repair idempotence, retain production activity and revoke temporary tokens.
- [x] Record verification and synchronize the shared workspace and Git metadata.

No remaining blocker in the requested polish scope. Legacy Sass deprecations and physical-device/Safari checks remain separate maintenance work.

---

# UNIVERSE continuation checklist

## Release and handoff, completed 2026-11-05

- [x] Confirm Forge deployment `82170163` finished successfully and `current` uses functional commit `6880d46`.
- [x] Confirm the temporary Twig/comments-script corrections are superseded by the new release; shared symlinks/generated assets can still appear in remote Git status.
- [x] Finish `/var/tmp/universe-community-production-browser.py` and inspect its report. Earlier failures in the same report filename are stale until rerun completes.
- [x] Confirm Reels and its mobile resize behavior, local badge URLs, Live preview playback, populated leaderboard tabs and Chat for administrator/student/organizer on production.
- [x] Check `/health` and Doctrine schema validation on the actual active release.
- [x] Revoke the exact temporary Forge API token `UNIVERSE Help Center deployment 2026-11-05`; preserve user-owned tokens.
- [x] Delete temporary raw token, Forge session/cookie page captures and private QA cookie/token captures.
- [x] Update `CONTEXT.md` with final deployment/browser results; sync changed files and Git metadata to the shared workspace.
- [x] Commit/push the requested `CLAUDE.md`, `CONTEXT.md`, `TODO.md` handoff with no secrets.

## Completed today

- [x] Modernize backend/frontend dependencies, schema/entity mapping and legacy integrations.
- [x] Deploy the correct Forge site; preserve sessions, private files and demo credentials across releases.
- [x] Apply ownership/CSRF protections and private document access fixes.
- [x] Research Deree organizations, venues, facilities and historical events; establish the full fictional activity demo.
- [x] Repair missing local auth artwork, timezone data and scheduled publication/reminders.
- [x] Restore header/footer/mobile navigation and all four menu editors without redesign.
- [x] Seed 72 full Help Center answers, nine categories and 12 FAQs; verify all 83 related pages and search live.
- [x] Repair Reels pagination/search/media/likes/comments/uploads and owner checks.
- [x] Repair Live player/management/visibility/ownership/delete/restore behavior.
- [x] Repair Leaderboards roles/ranking/filter/reset and install original local badge art.
- [x] Repair Chat creation, participant isolation, message persistence/retry/history/read/mute/mobile/rate limits.
- [x] Add scoped community fixtures: 60 reels, four previews, five curated groups and varied fictional scores.
- [x] Repair malformed research JSON and verify full seeding twice on an independent fresh database, with unchanged 16,026 fixture records.
- [x] Remove the temporary full-seed database/grant and server-only full-seed/history scripts.
- [x] Pass 17 automated tests / 474 assertions and syntax/schema/container checks on corrected code.
- [x] Move the native comments script outside the bundle directory deleted by `assets:install`.
- [x] Correct Reels resize animation and the subsequent Twig `{#` parsing mistake; add explicit deployment lint.

## Follow-up work, only after the release is verified

These are maintenance candidates, not confirmed new production blockers:

- [ ] Move the useful temporary integration/browser tests into reproducible repository tooling with private credentials kept outside Git.
- [ ] Consider a deployment smoke gate for individual critical pages, since `/health` does not render Twig.
- [ ] Review remaining legacy form/upload mappings and old hard-coded URLs as further user reports arrive; the current pass repaired the demonstrated failures.
- [ ] Review remaining legacy Sass/build deprecation warnings separately. Current runtime browser warnings should be zero; do not confuse build warnings with runtime failures.
- [ ] If the user wants genuine broadcasts, configure their real YouTube/Vimeo/Twitch stream URLs. Current seeded sessions are clearly labelled local previews.
- [ ] If requested, extend chat beyond current text/group/history/read/mute functionality (attachments, typing/presence, edit/delete synchronization). Do not claim those optional features already exist or provision new services without need.
- [ ] Keep official ACG help references current when campus procedures change; do not invent College policy or real student data.

## Guardrails for continuation

- Preserve the original UI. Do not restart a redesign.
- Do not rerun full demo seeding on production for a small content update.
- Keep QA on `universe_nav_20261105`; never repoint it to production for mutation tests.
- Do not redeploy `cf2685a` or rely solely on cache warmup to validate Twig.
- Never put passwords, private `.env` values, Forge tokens or browser cookies in these public files.
