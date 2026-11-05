# Navigation

The original Bootstrap theme remains in use. Header and footer links share `MenuNavigation`, which reads the four database menus and applies role rules and safe URL validation. Menus can be empty without breaking the layout.

Edit navigation at **Dashboard > Settings > Menus** as an administrator. Menu names identify the slots; headings appear above footer columns. Each item has a required label, an optional icon, exactly one destination (a page selection or custom URL), and a visibility setting. Header items also choose between the main row and the **More** dropdown. Add, remove, and move controls update persisted positions. Removal is immediate, including when Save is clicked straight away. All entries can be removed to hide a menu. The editor offers 50 event destinations to keep large demos manageable; existing selected events remain selectable, and any event can be linked by custom path.

| Slot | Default links |
| --- | --- |
| Header | Events, Creators, Venues, Athletics, Listings, News; role-specific My Tickets or Add Event |
| Header More | Reels, Live, Leaderboards, Help center, About us, Contact; organizers can scan tickets |
| First footer | About us, Help center, Privacy policy, Cookie policy, Terms of service, Contact |
| Second footer | Guest registration or signed-in dashboard, notifications, chat, password/email settings; attendee tickets |
| Third footer | Campus discovery sections |

Guests do not see authenticated destinations. Attendee, organizer, and administrator paths require the corresponding role even when an item is configured for Everyone. The application still checks authorization at each destination. On phones, **Account menu > Dashboard menu** opens the original dashboard drawer; Escape or its close button returns focus to the account control. Categories can be configured as a special dynamic menu entry.

`app:menus:sync` installs or repairs the demo-owned defaults without reseeding accounts, bookings, or activity. Deployments run it after migrations. A content hash records the installed version, so subsequent deployments preserve administrator edits. `--force` explicitly reapplies demo-owned defaults; administrator-created entries are retained. Full demo seeding uses the same `data/ui-menus.json` defaults.

`Live` now lists configured visible sessions and resolves each record by slug. A missing or hidden session returns 404. Demo sessions explain that streaming is not configured, rather than embedding an unrelated channel on an obsolete hostname. Terms of service links to the demo's configured legal notice.

Verification: `TMPDIR=/var/tmp vendor/bin/phpunit`, Symfony container/Twig lint, Doctrine schema validation after migration, and browser checks for guest, attendee, organizer, and administrator navigation. Exercise heading edits, item addition/removal/reordering, destination validation, mobile collapse, and dropdowns on a separate database before deployment.
