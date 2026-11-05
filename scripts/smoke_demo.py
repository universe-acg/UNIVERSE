#!/usr/bin/env python3
"""Read-only authenticated page checks. Never prints passwords, cookies or page bodies."""
import argparse,json,re
from pathlib import Path
import requests
from bs4 import BeautifulSoup

ap=argparse.ArgumentParser();ap.add_argument('--base',default='http://127.0.0.1:8098');args=ap.parse_args()
credentials=json.loads(Path('var/demo-credentials.json').read_text());report=[]
paths={
 'student':['/dashboard/','/events','/creators','/venues','/athletics','/listings','/leaderboards','/dashboard/attendee/','/dashboard/attendee/account/settings','/dashboard/notifications'],
 'organizer':['/dashboard/','/dashboard/organizer/','/dashboard/organizer/my-events','/dashboard/organizer/manage-listings','/dashboard/organizer/managementgb'],
 'administrator':['/dashboard/','/dashboard/administrator/','/dashboard/administrator/manage-users','/dashboard/administrator/manage-events','/dashboard/administrator/manage-categories','/dashboard/administrator/manage-blog-posts','/dashboard/administrator/administrator/signup/attendee'],
}
for role,username in credentials['accounts'].items():
 session=requests.Session();session.headers['User-Agent']='UNIVERSE verification/1.0'
 page=session.get(args.base+'/signin',timeout=40);soup=BeautifulSoup(page.text,'html.parser');token=soup.select_one('input[name="_csrf_token"]')
 if not token: report.append({'role':role,'login':'no login form','status':page.status_code});continue
 result=session.post(args.base+'/login_check',data={'_username':username,'_password':credentials['password'],'_csrf_token':token['value']},timeout=60,allow_redirects=False)
 report.append({'role':role,'login_status':result.status_code,'location':result.headers.get('Location')})
 for path in paths[role]:
  try:
   result=session.get(args.base+path,timeout=90);soup=BeautifulSoup(result.text,'html.parser')
   title=soup.title.get_text(' ',strip=True) if soup.title else ''
   # Exception titles are enough to diagnose; never store user page bodies.
   report.append({'role':role,'path':path,'status':result.status_code,'url':result.url,'title':title[:400]})
   print(role,path,result.status_code,title[:180],flush=True)
  except Exception as error:report.append({'role':role,'path':path,'error':type(error).__name__});print(role,path,type(error).__name__,flush=True)
Path('var/smoke-report.json').write_text(json.dumps(report,indent=2)+'\n')
