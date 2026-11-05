# Community sections

The existing Bootstrap interface is retained. Reels uses the original masonry grid and media dialog, Live uses the existing session cards, Leaderboards retains its tables and level badges, and Chat retains its conversation cards and message bubbles.

## Reels

Public results use one deterministic, database-backed page of 24 records. Load More returns real page counts and never duplicates a record. Search covers titles, captions and tags. Hidden or deleted posts are excluded from both the gallery and detail pages. The dialog plays the selected file, supports previous/next, Escape, keyboard focus and image/video handling. Detail pages expose idempotent like/unlike and existing threaded comments. Personal legacy like endpoints restrict access and return limited public data.

Image and MP4/WebM uploads work through Vich with unique filenames, validated files, 1,000-character captions and owner checks. Shared fixture assets are retained when a record changes, so editing one example cannot remove the media used by others.

## Live sessions

Sessions now support an owner, a cover, complete descriptions, URLs up to 2,048 characters, visibility and Preview/Upcoming/Live/Ended states. Organizer management supports create/edit, hide/show, logical deletion and restoration. An organizer cannot manage another organizer's records.

YouTube, Vimeo and Twitch URLs resolve to approved player origins. Twitch receives the current site's hostname as its parent. Local MP4/WebM fixtures use a native video player; other HTTPS links remain explicit external links. Arbitrary HTML embeds and non-HTTPS provider URLs are rejected. The four seeded sessions are clearly marked previews, with locally hosted illustrative video, rather than fabricated live College broadcasts.

## Leaderboards

Enabled, undeleted attendee and organizer accounts are selected by their actual roles and sorted by score, then username for stable ties. Administrator accounts are excluded from attendee rankings. Tabs, level filters, text filtering, deep links and reset share one state without replacing the table DOM. Empty matches and reduced motion are handled. Demonstration scores are fictional participation examples.

## Chat

A signed-in user can start a direct conversation by username or confirm one initiated from a profile link. An atomic unique participant-pair key reuses direct rooms even when two starts arrive simultaneously. Existing group rooms remain separate. Leaving clears the direct key, so a later conversation cannot silently rejoin the history of a room the user left.

Every room and message endpoint checks membership. Messages are plain escaped text, support Unicode and up to 4,000 characters, and require CSRF on writes. A per-user limit permits 60 send requests per minute across current and legacy endpoints. Per-message client IDs make retries idempotent, including an ambiguous network failure after the server saved the message.

The browser polls every three seconds while visible, merges messages without gaps or duplicates, retains drafts after failed sends, and shows connection errors. It renders message text through `textContent`. Initial history contains the newest 200 messages; older pages preserve chronological order and scroll position. Read cursors and mute state persist per participant. Mobile users can access the conversation list and start form. Existing edit/delete APIs remain sender-only.

## Demo seeding and verification

`app:community:seed` installs 60 distinct examples (50 image posts and 10 video posts), four playable Live previews, five curated group conversations with 20 messages, and deterministic scores for registered demo accounts with zero scores. Existing conversations and account activity are preserved. A version marker prevents later deployments from resetting user changes; `--force` explicitly reapplies registered examples. `app:demo:seed` uses the same definitions. Media artwork and video are original illustrative fixtures in `data/demo-media`, copied to the existing shared uploads directory. No external streaming credentials are required for playback tests.

Verification includes PHPUnit, schema/container/Twig lint, real image/video uploads, searchable nonduplicating pagination, video playback, comments, like/unlike, leaderboard tabs/filters/reset, organizer ownership and Live restoration. Chat tests cover two browser sessions, message exchange, Unicode/XSS handling, CSRF, unauthorized room access, older history, read/mute state, sender-only mutations, send limits and retry after a simulated network drop following a successful server save.

A fresh full-seed check also exercises every imported JSON dataset. Three historical research exports contained unescaped newlines; their JSON encoding has been repaired without changing the values. Deployments validate all Deree JSON inputs before changing the database.
