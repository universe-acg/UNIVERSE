#!/usr/bin/env python3
"""Read EventON's public calendar API by year; store facts, never full event prose."""
import argparse,datetime,json,re,shlex,subprocess
from pathlib import Path
from urllib.parse import urlencode
from bs4 import BeautifulSoup

URL='https://www.acg.edu/news-events/department-of-college-events-office-of-the-president/past-events/'
def main():
 ap=argparse.ArgumentParser();ap.add_argument('--ssh-source',required=True);args=ap.parse_args()
 def remote(command):return subprocess.check_output(['ssh','-o','BatchMode=yes',args.ssh_source,command],timeout=90).decode()
 soup=BeautifulSoup(remote('curl --fail -Ls --max-time 60 '+shlex.quote(URL)),'html.parser')
 def values(selector):return {k[5:]:v for k,v in soup.select_one(selector).attrs.items() if k.startswith('data-')}
 shortcode=values('.cal_arguments');evodata=values('.evo-data')
 out=Path('data/deree');out.mkdir(parents=True,exist_ok=True)
 facts=json.loads((out/'event_facts.json').read_text()) if (out/'event_facts.json').exists() else []
 windows=[];empty=0;year=datetime.date.today().year
 while empty<3:
  start=int(datetime.datetime(year,1,1,tzinfo=datetime.timezone.utc).timestamp());end=int(datetime.datetime(year+1,1,1,tzinfo=datetime.timezone.utc).timestamp())-1
  sc={**shortcode,'number_of_months':'12','month_incre':'0','fixed_month':'1','fixed_year':str(year),'hide_past':'no','show_limit':'no','event_count':'10000'}
  ed={**evodata,'cmonth':'1','cyear':str(year),'range_start':str(start),'range_end':str(end),'ev_cnt':'10000'}
  data={'action':'the_ajax_hook','direction':'none','sort_by':'sort_date','ajaxtype':'jumper'}
  for key,value in sc.items():data['shortcode['+key+']']=value
  for key,value in ed.items():data['evodata['+key+']']=value
  try:
   raw=remote('curl --fail -Ls --max-time 60 -X POST --data '+shlex.quote(urlencode(data))+' https://www.acg.edu/wp-admin/admin-ajax.php')
   response=json.loads(raw)
   debug=Path('var/research');debug.mkdir(parents=True,exist_ok=True);(debug/('calendar-'+str(year)+'.json')).write_text(raw)
   if not isinstance(response,dict) or 'content' not in response:raise ValueError('Unexpected public calendar response')
   html=BeautifulSoup(response['content'],'html.parser');count=0
   for event in html.select('.eventon_list_event'):
    title=event.select_one('.evcal_event_title');time=event.get('data-time')
    if not time:
     node=event.select_one('[data-time]');time=node.get('data-time') if node else None
    if not title or not time:continue
    a,b=map(int,time.split('-'));dt=datetime.datetime.fromtimestamp(a,datetime.timezone.utc)
    if dt.year!=year:continue
    link=event.select_one('a[href*="/events/"]')
    source_url=link.get('href') if link else URL
    if source_url.startswith('//'):source_url='https:'+source_url
    facts.append({'title':title.get_text(' ',strip=True),'source_id':event.get('data-event_id',event.get('id')),
     'source_url':source_url,'start':dt.replace(tzinfo=None).isoformat(),'end':datetime.datetime.fromtimestamp(b,datetime.timezone.utc).replace(tzinfo=None).isoformat(),
     'verification':'official_calendar','retrieved_at':datetime.date.today().isoformat(),'calendar_year':year})
    count+=1
   if count==0 and any(r['start'].startswith(str(year)) for r in facts):raise ValueError('Calendar API returned no events for a year with verified archive events; extraction is incomplete.')
   windows.append({'year':year,'events':count,'status':'ok'});empty=empty+1 if not count else 0
   print(year,count,flush=True)
  except Exception as error:
   windows.append({'year':year,'status':'failed','error':str(error)});print(year,str(error),flush=True);break
  year-=1
  # A broken API ignoring requested years must not create an endless crawl.
  if year<1900:break
 unique={(r['title'].casefold(),r['start']):r for r in facts}
 (out/'event_facts.json').write_text(json.dumps(sorted(unique.values(),key=lambda r:r['start']),ensure_ascii=False,indent=2)+'\n')
 (out/'history_coverage.json').write_text(json.dumps({'source_url':URL,'retrieved_at':datetime.date.today().isoformat(),'year_windows':windows,'events':len(unique),'boundary':'three consecutive empty preceding years','complete':not any(w['status']=='failed' for w in windows)},indent=2)+'\n')
 print('Unique events:',len(unique))
if __name__=='__main__':main()
