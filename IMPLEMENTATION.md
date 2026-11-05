# UNIVERSE deployment and verification

Original UI must be preserved. User explicitly rejected a redesign. Keep the
original Twig layouts, orange theme, logo, form theme and navigation structure.
Latest user request authorizes repairing broken image URLs and supplying demo
credentials. Local copies of existing project artwork replace unavailable remote
image assets; no external image license was introduced.

Production: https://universe-deree-live-3b97bb.on-forge.com
Forge site 3483627, server helppoint-production / 950296, organization slug
alexandros-kourtis. GitHub universe-acg/UNIVERSE, master. Symfony 7.4 / PHP 8.2.
Root /home/forge/universe-deree-live-3b97bb.on-forge.com/current.
The isolated MySQL database is universe_demo_20261105. Credentials, uploads,
private files and sessions persist through configured Forge shared paths.

All 55 mapped entity types have demo records. 596 fictional accounts, 93 researched
student groups plus the archive caretaker, 2,000 registrations and tickets, 60
opportunities, 180 applications, 120 messages and related demo content are seeded.
Source archive: 1,372 official event pages plus four calendar-only session rows;
1,364 pages have verified local event-date microdata. Seven undated records remain
drafts. Dates of publication are never substituted for event dates. Accessible
sources cannot prove completeness of deleted or undocumented historical content.

Production main administrator/student/organizer smoke checks passed. Full route
handler audit: 414 routes, zero unresolved handlers. 221 Twig templates lint.
PHPUnit: four tests, twelve assertions. Schema validation passed. Composer and
npm audits report no known vulnerabilities in installed dependencies.

The browser/page crawl found guest profile, category query and invalid reel-link
failures; fixes are committed. Additional role/CRUD, concurrency and export
verification is still warranted. Do not claim every possible interaction was
exhaustively tested. Staging uses loopback 127.0.0.1:8301 and local SSH tunnel
127.0.0.1:18301; it is not the public deployment.

Working copy: /var/tmp/universe-modernization-20261105. Synchronize all final
changes and Git metadata back to the original SSHFS checkout. Never copy private
runtime credentials, raw research HTML, vendor, node_modules or cache into Git.
