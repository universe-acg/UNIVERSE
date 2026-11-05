# Help Center content

The reviewed catalogue is `data/deree/help-center.json`: 72 full answers in nine categories, with 12 featured questions reused on Contact/FAQ and category cards. Campus references link to official ACG sources and record a verification date. Answers distinguish website demonstrations from actual College services. Existing `demo-help-0` through `demo-help-3` and `using-the-demo` URLs remain valid.

`app:help-center:seed` installs the catalogue only when `APP_DEMO=1`. It updates registered demo-owned help records, preserves unrelated administrator content, leaves view counts intact, and never reseeds accounts or bookings. A content hash prevents repeated deployments from overwriting administrator edits. Use `--force` explicitly to reapply the reviewed defaults. Full `app:demo:seed` uses the same catalogue.

Administrators can edit articles under Dashboard > Help center, including tags, FAQ/highlight status and visibility. Article bodies support full HTML text; search tags can be reused. Hidden categories suppress their articles in public listings and search. Article titles can change without invalidating their established URLs.
