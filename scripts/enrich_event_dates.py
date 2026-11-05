#!/usr/bin/env python3
"""Read official Event microdata, including EventON pages with malformed JSON-LD.
Raw source stays in private runtime storage. Local page times are Athens wall time.
"""
import argparse,concurrent.futures,datetime,hashlib,json,re,time
from pathlib import Path
import requests
from bs4 import BeautifulSoup
from dateutil.parser import isoparse

def official_date(value):
 value=re.sub(r"^(\d{4})-(\d{1,2})-(\d{1,2})",lambda m:f"{int(m[1]):04d}-{int(m[2]):02d}-{int(m[3]):02d}",value)
 return isoparse(value)

def main():
 ap=argparse.ArgumentParser();ap.add_argument('--workers',type=int,default=5);ap.add_argument('--only-missing',action='store_true');args=ap.parse_args()
 path=Path('data/deree/event_facts.json');rows=json.loads(path.read_text());cache=Path('var/research/event-pages');cache.mkdir(parents=True,exist_ok=True)
 urls=sorted({r['source_url'] for r in rows if r['source_url'].startswith('https://www.acg.edu/events/') and (not args.only_missing or not r.get('start'))});updates={};failures=[]
 def fetch(url):
  file=cache/(hashlib.sha256(url.encode()).hexdigest()+'.html')
  try:
   if file.exists():html=file.read_text()
   else:
    response=requests.get(url,timeout=45);response.raise_for_status();html=response.text;file.write_text(html)
   soup=BeautifulSoup(html,'html.parser');candidates=soup.select('[itemtype$="/Event"]')
   for event in candidates:
    link=event.select_one('[itemprop="url"]')
    if link and link.get('href','').rstrip('/')!=url.rstrip('/'):continue
    start=event.select_one('[itemprop="startDate"]');end=event.select_one('[itemprop="endDate"]')
    if not start:continue
    value=start.get('content') or start.get('datetime') or start.get_text(strip=True);date=official_date(value)
    if not 1900<date.year<datetime.date.today().year+5:continue
    result={'start':date.isoformat(),'date_status':'official_event_microdata','date_precision':'day' if len(value)==10 else 'minute','timezone':'Europe/Athens'}
    if end:
     value=end.get('content') or end.get('datetime') or end.get_text(strip=True)
     try:result['end']=official_date(value).isoformat()
     except ValueError:pass
    venue=event.select_one('[itemprop="location"] [itemprop="name"]')
    if result.get('end') and official_date(result['end']) < date:result.pop('end');result['end_status']='Official end date precedes start; excluded'
    if venue:result['venue_text']=venue.get('content') or venue.get_text(' ',strip=True)
    return url,result,None
   return url,None,'No event microdata date found'
  except Exception as error:return url,None,type(error).__name__
 def save(reviewed):
  changed=0
  for row in rows:
   if row['source_url'] in updates:row.update(updates[row['source_url']]);changed+=1
  tmp=path.with_suffix('.json.tmp');tmp.write_text(json.dumps(rows,ensure_ascii=False,indent=2)+'\n');tmp.replace(path)
  Path('data/deree/date_coverage.json').write_text(json.dumps({'reviewed':reviewed,'target_pages':len(urls),'verified_unique_pages':len(updates),'verified_event_rows':changed,'remaining_undated':sum(not r.get('start') for r in rows),'failures':failures,'timezone':'Official page microdata uses local wall time; stored as Europe/Athens without shifting UTC.'},indent=2)+'\n')
 with concurrent.futures.ThreadPoolExecutor(max_workers=args.workers) as pool:
  for number,(url,values,error) in enumerate(pool.map(fetch,urls),1):
   if values:updates[url]=values
   else:failures.append({'source_url':url,'error':error})
   if number%25==0 or number==len(urls):save(number);print('Reviewed',number,'of',len(urls),'verified',len(updates),flush=True)
if __name__=='__main__':main()
