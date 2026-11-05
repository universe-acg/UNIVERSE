#!/usr/bin/env python3
"""Reconcile calendar/feed overlap by official event ID, retaining distinct sessions."""
import hashlib,json,re
from pathlib import Path
from collections import defaultdict
from bs4 import BeautifulSoup
path=Path('data/deree/event_facts.json');rows=json.loads(path.read_text());canonical={};by_id={};extras=[]
for row in rows:
 if '/events/' not in row['source_url']:extras.append(row);continue
 url=row['source_url'];file=Path('var/research/event-pages')/(hashlib.sha256(url.encode()).hexdigest()+'.html')
 if file.exists():
  match=re.search(r'\bpostid-(\d+)',file.read_text())
  if match:row['official_event_id']=match[1];by_id[match[1]]=row
 canonical[url.rstrip('/')]=row
unmatched={};merged=0
for row in extras:
 event=by_id.get(str(row.get('source_id')))
 if event:
  event.setdefault('additional_sources',[])
  if row['source_url'] not in event['additional_sources']:event['additional_sources'].append(row['source_url'])
  merged+=1;continue
 key=(row.get('source_id'),row.get('start'),row.get('end'));unmatched[key]=row
final=list(canonical.values())+list(unmatched.values());path.write_text(json.dumps(final,ensure_ascii=False,indent=2)+'\n')
report={'official_event_pages':len(canonical),'calendar_rows_matched_to_event_pages':merged,'additional_calendar_sessions':len(unmatched),'total_event_records':len(final),'dated_records':sum(bool(r.get('start')) for r in final),'undated_records':sum(not r.get('start') for r in final),'limits':['Undated official records remain draft in the app.','Published event history is bounded by currently accessible official sources. Deleted pages and undocumented organizations cannot be asserted complete.']}
Path('data/deree/reconciliation.json').write_text(json.dumps(report,indent=2)+'\n');print(json.dumps(report,indent=2))
