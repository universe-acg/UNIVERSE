# UNIVERSE: continuation instructions

Read `/home/alexandroskourtis/.claude/CLAUDE.md` when present, then applicable project instructions. Read `CONTEXT.md` and `TODO.md` before continuing. User shorthand **UC** means update these three handoff files.

## User intent and constraints

- Repair, populate, push and deploy this existing project on its established Forge site.
- Preserve the original Universe interface, colors, artwork and layout. Restore broken functions; do not redesign.
- The user has repeatedly authorized deployment and necessary server work. Do not repeat permission questions for this work.
- Most recent functional scope: complete FAQ/Help Center, Reels, Live, Leaderboards and Chat, including realistic demo data.
- Ignore the earlier Google Calendar synchronization message, as explicitly requested.
- Keep updates concise. Never claim a release is healthy until its actual public pages have been checked.

## Workspace and commands

- Shared workspace: `/home/alexandroskourtis/homelab/shared/newwork/UNIVERSE`.
- Fast working copy: `/var/tmp/universe-modernization-20261105`. The shared path is slow; work in the fast copy, then sync changed tracked files **and Git metadata** back. Check both copies before overwriting any new user edits.
- Branch: `master`; remote: `git@github.com:universe-acg/UNIVERSE.git`.
- This repository is public. Never commit credentials, cookies, tokens, `.env.local`, private delivery captures or demo credential files.
- Use `TMPDIR=/var/tmp` for Node, Python and browser commands. `/tmp` has exhausted its quota and can cause error `-122`.
- Prefer Bash with `login=false`; use `rg` for searches. No parallel agents have been authorized.

Routine checks:

```bash
php scripts/validate_demo_data.php
TMPDIR=/var/tmp vendor/bin/phpunit --no-progress
php bin/console lint:container --env=prod --no-debug
php bin/console lint:twig templates --env=prod --no-debug
php bin/console lint:yaml config/packages/vich_uploader.yaml config/packages/rate_limiter.yaml
```

Also check changed standalone JavaScript with `node --check`. Run Doctrine schema validation on an appropriate server database after migration. Browser test the actual flows affected, including mobile and error cases.

## Production and seeding

- URL: `https://universe-deree-live-3b97bb.on-forge.com`.
- Forge: organization `alexandros-kourtis`, server `helppoint-production` / `950296`, site `3483627`.
- SSH: `forge@64.227.66.233`.
- Site root: `/home/forge/universe-deree-live-3b97bb.on-forge.com`; application is its `current` symlink.
- Database: `universe_demo_20261105`, MySQL 8. Credentials remain in private `.env.local`.
- Deployment entry point: `deploy/forge.sh`, invoked by Forge's release/activate macros. Push-to-deploy is disabled; a push alone does not deploy.
- Keep custom compatibility JavaScript in `public/js`, not `public/bundles`: Symfony `assets:install` deletes the retired bundle directory.
- Explicit Twig lint is required before activation. Cache warmup alone did not catch a real Twig parsing error today.
- `/health` is necessary but insufficient. It can return 200 while an individual Twig page fails.
- Preserve existing production activity. Routine deployments run only idempotent scoped menu, help and community commands; **do not rerun the full demo seeder on production** just to update content.
- Full seed: `app:demo:seed`; scoped updates: `app:menus:sync`, `app:help-center:seed`, `app:community:seed`. `--force` deliberately resets their demo-owned defaults.
- Scoped version markers preserve later administrator edits. Community versioning currently hashes both its JSON definitions and `CommunityDemoData.php`; changing that service can trigger reapplication. Badge installation was therefore added in a separate script without changing the catalogue hash.
- Current schema migrations include irreversible data-expansion migrations. Do not attempt an unreviewed production migration rollback.

## Security and infrastructure

- Keep production debug off. Keep private uploads, sessions, credentials and captured mail outside `public/`.
- Demo email/SMS is captured, not sent to real people. Do not enable real delivery as part of testing.
- Chat uses authenticated HTTP polling; the optional WebSocket service is loopback-only and is not required.
- Preserve existing ownership, role and CSRF checks. Do not reopen GET mutation routes for convenience.
- Do not change unrelated sites or processes on this shared Forge server.
- Temporary Forge API tokens used for deployment must be revoked after work; delete local cookie/token page captures. See `CONTEXT.md` for access-helper locations, never put their contents in chat or Git.

Further implementation notes: `docs/navigation.md`, `docs/help-center.md`, `docs/community.md`, `deploy/README.md`.

## Public UI continuation, 2026-11-05

- Latest public-page repair is deployed as application `52ab54c`, Forge `82246870`; read the newest section of `CONTEXT.md` first.
- Preserve the public design context in `.impeccable.md` and shared styles in `assets/css/public.scss`. Keep dashboard-only styling separate.
- Do not restore fixed filter sidebars, column offset hacks, artificial Listings/News duplication, Maps-dependent profile tabs or legacy runtime thumbnail URLs.
- The header wordmark uses `app:brand:install`; retain its CSS capture-edge clipping and preserve later administrator logo changes.
- Scoped deployment commands `app:demo:repair-images` and `app:pages:sync` are intentional and repeat-safe. Do not replace them with a full production seed.
- Community fixture versioning deliberately excludes asset-only code changes, preserving edited demo activity.
- The read-only public smoke test is `tests/browser/public_ui.py`; `docs/public-ui.md` documents the repaired behaviors and QA checks.
