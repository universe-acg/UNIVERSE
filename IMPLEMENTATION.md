# UNIVERSE restoration status

## Latest handoff, supersedes the initial notes below

**User correction: DO NOT CHANGE THE UI.** Replacement authentication screens were removed. Original Twig layouts, custom form theme, orange theme, original logo assets, visual settings and 19 navigation items have been restored. Original stylesheet sources remain unchanged. Adapt backend contracts to existing templates; do not redesign pages or introduce new visual systems.

- Fast copy with latest edits: `/var/tmp/universe-modernization-20261105`. Original SSHFS checkout must receive the latest delta, excluding private runtime files, dependencies and source caches.
- Staging: `/home/forge/universe-demo-staging/app` on `forge@64.227.66.233`. Its PHP development server listens only on `127.0.0.1:8301`; local SSH forwarding uses `127.0.0.1:18301`. Both opcache.enable=0 and opcache.enable_cli=0 are required on the PHP development server to avoid stale code; disabling only enable_cli did not disable the cli-server SAPI cache. Staging is temporarily dev/debug on loopback; switch to prod/0 before publication.
- Isolated MySQL database: `universe_demo_20261105`; private credentials in `.env.local`. Local DB tunnel is `127.0.0.1:13376`; run seed passes on the server for speed. Existing hosted databases were not replaced.
- New dependencies: Symfony 7.4.23, Doctrine ORM 3, Encore 7/Babel 8, PHP 8.2, Node 22. Composer/npm audit locks reported zero advisories. Frontend build passes with legacy Sass deprecation warnings.
- ORM/schema validation passed. Native authentication, registration/recovery and private mail/SMS capture are implemented, but full workflow acceptance remains outstanding.
- Latest seed passed with 14,577 demo records. Earlier double-seed/schema checks passed; rerun idempotency after final changes. Generated accounts/passwords are private in `var/demo-credentials.json`; never print or commit them.
- Route audit now checks handler existence and declared argument types: 406 routes, zero unresolved handlers at last run. All 221 Twig files passed syntax checks. PHPUnit: 4 tests, 12 assertions pass.
- Main administrator/student/organizer dashboard and listing pages returned HTTP 200 with original UI. This is not a complete every-page/CRUD acceptance result. Use `scripts/smoke_demo.py`; some guessed URLs were corrected to real `/creators` and `/dashboard/attendee/account/settings` paths.
- Screenshot of restored sign-in: `/var/tmp/universe-original-ui.png`. Historical login/register background images are unavailable from their remote URLs; do not replace them with new artwork.
- Research: 127 directory records (63 clubs, 20 academic societies, 10 student organizations, 28 venues, 6 athletic facilities). Full RSS traversal found 1,372 event URLs across 139 populated pages, back to 2016. Current merged facts have 1,431 rows: 724 dated, 707 undated. Reconcile overlaps/conflicts. Publication dates are never substituted for event dates.
- Structured-date enrichment finished but resolved zero additional dates; see `data/deree/date_coverage.json`. Verify the terminating RSS HTTP status before claiming exhaustive coverage. Historical guidebooks/renamed organizations/institutional organizers still need reconciliation.
- Deliverable JSON contains factual fields/source references. Raw source prose is private under `var/research`; scripts must preserve that separation on reruns.
- Private document routing/storage and legacy POST/CSRF compatibility were recently added. Test these before release. Staging served stale route/Twig cache after source sync (GET mutation 403 and POST 405, helper config missing); clear BOTH prod and dev caches before retesting. Do not report these integration checks as passed. `public/js/legacy-actions.js` preserves existing visible controls while submitting protected actions.
- Forge account/API token searches across local/mounted `.env` files and targeted deployment histories found heartbeat/database references, but **no validated account API token**. User authorized searching this PC. Never print credentials or treat heartbeat tokens as account tokens.
- No Forge-managed/public site or TLS hostname has been provisioned. A valid Forge credential remains a publication prerequisite.

### Remaining acceptance work

1. Complete role/page/CRUD matrix, registration/recovery, authorization, CSRF, private downloads, booking/order concurrency, QR/exports and seed coverage/idempotency.
2. Repair legacy FOS comments template namespace/API/static assets, chat persistence/permissions, Ratchet/WebSocket replacement command, reminder command, incomplete athletics/listing services and optional-provider states. Existing route resolution does not prove action bodies work.
3. Finish source reconciliation, event date recovery, research-script hardening and all-organization historical coverage.
4. Verify fresh production install/build and provide deployment/supervisor/scheduler/health/backup/rollback setup; publish only after checks and Forge access are ready.

**The full restoration is not complete. Do not report all pages/features fixed or a live deployment.**

## Initial foundation notes (historical)

Target: Symfony 7.4 LTS, PHP 8.2, MySQL 8, Node 22. Preserve existing application identity and features. Dedicated Deree demo only; historical personal data is not imported.

## Completed foundations

- Dependency resolution on Symfony 7.4; new Composer lock has no reported advisories.
- Native Symfony authentication foundation and private demo mail capture.
- ORM, validator, routing and uploader attribute conversion.
- Corrected invalid entity associations; ORM mapping validation passes.
- Removed hardcoded runtime paths and replaced tracked environment secrets with development defaults.
- Initial official Deree research collection: clubs, societies, organizations, venues, athletic facilities and historical event metadata.
- Transactional, repeatable seed command under development, with a stable demo ownership registry and private generated credentials.

## In progress, not release-ready

- Fresh baseline migration and MySQL execution.
- Fixture relationship verification, complete entity coverage and source reconciliation.
- Frontend build and Twig 3 compatibility.
- Native registration/password recovery and remaining legacy service calls.
- Complete authorization/CSRF repair and every-page role matrix.
- Background workers and demo integration behavior.
- Browser tests, dependency audits, deployment and rollback documentation.

## Environment

- Fast working copy: `/var/tmp/universe-modernization-20261105`.
- Original checkout: `/home/alexandroskourtis/homelab/shared/newwork/UNIVERSE`.
- Authorized SSH access to `forge@64.227.66.233` confirmed.
- Isolated database: `universe_demo_20261105`; no existing application databases modified.
- Local database tunnel: `127.0.0.1:13376`; private configuration in `.env.local`.
- Forge account/API access is still needed to provision a Forge-managed temporary hostname. No live site has been published.

## Required acceptance gates

Fresh production install/build, clean schema validation, two successful seed passes without duplication, complete route/controller validation, role-based workflow checks, browser checks, source coverage report and deployed HTTPS smoke tests. Never report completion before these pass.
