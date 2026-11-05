#!/usr/bin/env python3
"""Traverse the official public event RSS archive; export factual metadata only."""
import argparse,concurrent.futures,datetime,json,re,shlex,subprocess
from pathlib import Path
from bs4 import BeautifulSoup
from dateutil.parser import parse as parse_date

BASE='https://www.acg.edu/?feed=rss2&post_type=ajde_events'
def main():
 ap=argparse.ArgumentParser();ap.add_argument('--ssh-source',required=True);args=ap.parse_args()
 output=Path('data/deree');private=Path('var/research/event-feed');private.mkdir(parents=True,exist_ok=True)
 facts=json.loads((output/'event_facts.json').read_text());pages=[];seen=set();done=False;page_number=1
 def fetch(number):
  url=BASE+'&paged='+str(number)
  try:
   raw=subprocess.check_output(['ssh','-o','BatchMode=yes',args.ssh_source,'curl --fail -Ls --max-time 60 '+shlex.quote(url)],timeout=75).decode()
   soup=BeautifulSoup(raw,'xml');items=soup.find_all('item')
   if not soup.find('rss'):raise ValueError('Response is not RSS')
   (private/(str(number)+'.xml')).write_text(raw)
   records=[]
   for item in items:
    url=item.link.get_text(strip=True);title=item.title.get_text(' ',strip=True)
    body=item.find('encoded') or item.description
    text=BeautifulSoup(body.get_text() if body else '', 'html.parser').get_text(' ',strip=True)
    when=re.search(r'\bWhen\s*:\s*(.*?)(?=\bWhere\s*:|\bOrganiz|\bAbout\b|$)',text,re.I)
    where=re.search(r'\bWhere\s*:\s*(.*?)(?=\bWhen\s*:|\bOrganiz|\bAbout\b|$)',text,re.I)
    record={'title':title,'source_url':url,'source_id':url,'verification':'official_event_feed','retrieved_at':datetime.date.today().isoformat(),'publication_date':item.pubDate.get_text() if item.pubDate else None,'when_text':when[1][:400] if when else None,'venue_text':where[1][:400] if where else None}
    if when and re.search(r'\b20\d{2}\b',when[1]):
     raw_date=when[1].split('|')[0].split('–')[0].strip()
     try:
      dt=parse_date(raw_date,fuzzy=True,default=datetime.datetime(1900,1,1))
      if 2000<=dt.year<=datetime.date.today().year+3:
       time=re.search(r'\b(\d{1,2}):(\d{2})',when[1])
       if time:dt=dt.replace(hour=int(time[1]),minute=int(time[2]))
       record['start']=dt.isoformat();record['date_status']='parsed_from_official_when_field'
     except (ValueError,OverflowError):pass
    records.append(record)
   return number,records,None
  except subprocess.CalledProcessError as error:
   # The final nonexistent WordPress feed page returns HTTP 404.
   return number,[],('http_error_'+str(error.returncode))
  except Exception as error:return number,[],str(error)
 while not done:
  batch=list(concurrent.futures.ThreadPoolExecutor(max_workers=3).map(fetch,range(page_number,page_number+6)))
  for number,records,error in sorted(batch):
   unique=[r for r in records if r['source_url'] not in seen]
   pages.append({'page':number,'items':len(records),'unique_items':len(unique),'error':error})
   if not records or not unique:done=True;break
   for row in unique:seen.add(row['source_url']);facts.append(row)
   print('Page',number,'events',len(seen),flush=True)
  page_number+=6
  # Write progress after each batch so interrupted research is reviewable.
  canonical={}
  for row in facts:
   key=(row['title'].casefold(),row.get('start',''))
   if key in canonical:
    old=canonical[key];canonical[key]={**row,**old}
   else:canonical[key]=row
  (output/'event_facts.json').write_text(json.dumps(list(canonical.values()),ensure_ascii=False,indent=2)+'\n')
  (output/'feed_coverage.json').write_text(json.dumps({'source_url':BASE,'pages':pages,'unique_event_urls':len(seen),'complete':done,'limitations':['Events without an explicit parseable date remain undated; publication dates are never substituted for event dates.','The terminating unavailable/empty page is recorded; any earlier transport errors require retry.']},indent=2)+'\n')
 print('Feed traversal finished; event URLs:',len(seen),flush=True)
if __name__=='__main__':main()
