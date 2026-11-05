#!/usr/bin/env python3
"""Follow actual same-origin page links per role; record statuses without secrets."""
import argparse,collections,json,re
from pathlib import Path
from urllib.parse import urljoin,urlsplit
import requests
from bs4 import BeautifulSoup
ap=argparse.ArgumentParser();ap.add_argument('--base',default='http://127.0.0.1:18301');ap.add_argument('--limit',type=int,default=140);args=ap.parse_args();credentials=json.loads(Path('var/demo-credentials.json').read_text());report=[]
for role in ['anonymous','student','organizer','administrator']:
 s=requests.Session()
 if role!='anonymous':
  p=s.get(args.base+'/signin',timeout=60);token=BeautifulSoup(p.text,'html.parser').select_one('[name="_csrf_token"]')['value'];s.post(args.base+'/login_check',data={'_username':credentials['accounts'][role],'_password':credentials['password'],'_csrf_token':token},timeout=60)
 queue=collections.deque(['/','/events','/creators','/venues','/athletics','/listings','/leaderboards','/blog','/chat','/dashboard/'] if role!='anonymous' else ['/','/events','/creators','/venues','/athletics','/listings','/leaderboards','/blog']);seen=set();mutations=[]; signatures=collections.Counter()
 while queue and len(seen)<args.limit:
  path=queue.popleft()
  if path in seen:continue
  seen.add(path)
  try:
   r=s.get(args.base+path,timeout=60);soup=BeautifulSoup(r.text,'html.parser') if 'text/html' in r.headers.get('content-type','') else None
   title=soup.title.get_text(' ',strip=True)[:350] if soup and soup.title else ''
   report.append({'role':role,'path':path,'status':r.status_code,'title':title})
   if r.status_code>=400:print(role,r.status_code,path,title[:180],flush=True)
   if not soup or r.status_code!=200:continue
   config=soup.select_one('#universe-action-config')
   if config:mutations=[re.compile('^'+re.sub(r'\{[^}]+\}','[^/]+',p)+'/?$') for p in json.loads(config.string)['paths']]
   for a in soup.select('a[href]'):
    target=urlsplit(urljoin(r.url,a['href']));p=target.path
    if target.netloc!=urlsplit(args.base).netloc or not p.startswith('/') or target.query or p in seen or p in queue:continue
    if re.search(r'\.(png|jpg|jpeg|gif|webp|svg|pdf|zip|xlsx|docx|js|css|ico|woff2?)$',p,re.I) or p.startswith(('/assets/','/uploads/','/media/','/logout','/_','/switch','/cdn-cgi/')):continue
    if any(pattern.match(p) for pattern in mutations):continue
    signature=re.sub(r'/(archive-[^/]+|demo-[^/]+|organizer-[^/]+|student-[^/]+|venue-[^/]+|facility-[^/]+|[0-9]+)(?=/|$)','/{record}',p)
    if signatures[signature]>=2:continue
    signatures[signature]+=1
    if p.startswith('/dashboard/'):queue.appendleft(p)
    else:queue.append(p)
   if len(seen)%25==0:print(role,'checked',len(seen),'queued',len(queue),flush=True)
  except Exception as error:report.append({'role':role,'path':path,'error':type(error).__name__});print(role,'ERROR',path,type(error).__name__,flush=True)
  Path('var/crawl-report.json').write_text(json.dumps(report,indent=2)+'\n')
 print(role,'finished',len(seen),'pages',flush=True)
print('Total',len(report),'HTTP errors',sum(r.get('status',0)>=400 for r in report),flush=True)
