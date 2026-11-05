#!/usr/bin/env python3
"""Collect public ACG directory facts and event metadata. Never runs during seeding.

Requires beautifulsoup4; --ssh-source optionally uses an authorized server's egress.
Stored excerpts support review, not verbatim republication as application descriptions.
"""
import argparse
import concurrent.futures
import datetime
import hashlib
import json
from pathlib import Path
import re
import subprocess
import urllib.request
from urllib.parse import urljoin, urlparse
from bs4 import BeautifulSoup

BASE = 'https://www.acg.edu'
DIRECTORY = BASE + '/current-students/student-services/student-life/getting-involved/'
SOURCES = {
    'club': DIRECTORY + 'student-clubs/',
    'society': DIRECTORY + 'academic-societies/',
    'organization': DIRECTORY + 'student-organizations/',
    'venue': BASE + '/news-events/department-of-college-events-office-of-the-president/venues/',
    'facility': BASE + '/current-students/athletics/athletic-facilities/',
    'archive': BASE + '/news-events/department-of-college-events-office-of-the-president/past-events/',
}

def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--ssh-source')
    ap.add_argument('--output', default='data/deree')
    args = ap.parse_args()
    out = Path(args.output)
    out.mkdir(parents=True, exist_ok=True)
    retrieved = datetime.datetime.now(datetime.timezone.utc).isoformat()
    report = {'retrieved_at': retrieved, 'sources': [], 'failures': []}
    def fetch(url):
        if urlparse(url).hostname != 'www.acg.edu':
            raise ValueError('Only official ACG pages accepted')
        if args.ssh_source:
            # URL validation plus argument quoting avoids shell expansion remotely.
            import shlex
            html = subprocess.check_output(['ssh', '-o', 'BatchMode=yes', args.ssh_source,
                'curl --fail -Ls --max-time 45 ' + shlex.quote(url)], timeout=60).decode()
        else:
            html = urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': 'UNIVERSE public research/1.0'}), timeout=45).read().decode()
        return BeautifulSoup(html, 'html.parser')
    def content(soup):
        return soup.select_one('.entry-content') or soup.select_one('article') or soup.select_one('main') or soup
    records = []
    event_urls = set()
    archive_events = []
    detail_urls = set()
    for kind, url in SOURCES.items():
        try:
            soup = fetch(url)
            root = content(soup)
            report['sources'].append({'url': url, 'kind': kind, 'retrieved_at': retrieved,
                'sha256': hashlib.sha256(str(root).encode()).hexdigest()})
            if kind in ('club', 'society'):
                for heading in root.select('h2'):
                    name = heading.get_text(' ', strip=True)
                    if not name or name in ['More to Explore!', 'Venues']: continue
                    parts = []
                    for sib in heading.next_siblings:
                        if getattr(sib, 'name', None) in ['h2', 'hr']: break
                        if hasattr(sib, 'get_text'): parts.append(sib.get_text(' ', strip=True))
                    text = ' '.join(parts)
                    records.append({'name': name, 'kind': kind, 'source_url': url,
                        'retrieved_at': retrieved, 'verification': 'official_directory',
                        'research_notes': text[:5000], 'emails': re.findall(r'[\w.+-]+@[\w.-]+\.[A-Za-z]{2,}', text)})
            elif kind == 'organization':
                for a in root.select('a[href]'):
                    target = urljoin(url, a['href'])
                    name = a.get_text(' ', strip=True)
                    if target.startswith(url) and target != url and name and target not in detail_urls:
                        detail_urls.add(target)
                        records.append({'name': name, 'kind': kind, 'source_url': target,
                            'retrieved_at': retrieved, 'verification': 'official_directory'})
            elif kind in ('venue', 'facility'):
                for h in root.select('h2, h3'):
                    name = h.get_text(' ', strip=True)
                    if name and name not in ['Venues', 'Opening hours:', 'ACG spaces available for events', 'More to Explore!']:
                        records.append({'name': name, 'kind': kind, 'source_url': url,
                            'retrieved_at': retrieved, 'verification': 'official_directory'})
            if kind == 'archive':
                for event in soup.select('.eventon_list_event'):
                    title = event.select_one('.evcal_event_title')
                    if not title: continue
                    start = event.get('data-time', '')
                    times = event.select_one('[data-time]')
                    if not start and times: start = times.get('data-time', '')
                    archive_events.append({'title': title.get_text(' ', strip=True),
                        'source_url': url, 'source_id': event.get('data-event_id', event.get('id')),
                        'time_data': start, 'research_notes': event.get_text(' ', strip=True)[:10000]})
            for a in soup.select('a[href]'):
                target = urljoin(url, a['href']).split('#')[0]
                if target.startswith(BASE + '/events/') and '?' not in target: event_urls.add(target)
        except Exception as e:
            report['failures'].append({'url': url, 'error': str(e)})
        print(kind, len(records), 'records;', len(event_urls), 'event URLs', flush=True)
    def event_detail(url):
        try:
            soup = fetch(url)
            root = content(soup)
            title = soup.select_one('h1') or soup.select_one('.evcal_event_title')
            return {'title': title.get_text(' ', strip=True) if title else '',
                'source_url': url, 'retrieved_at': retrieved,
                'research_notes': root.get_text(' ', strip=True)[:16000],
                'structured_data': [s.string for s in soup.select('script[type="application/ld+json"]') if s.string]}
        except Exception as e: return {'source_url': url, 'error': str(e)}
    events = list(concurrent.futures.ThreadPoolExecutor(max_workers=3).map(event_detail, sorted(event_urls)))
    unique = {(r['kind'], r['name'].casefold()): r for r in records}
    records = sorted(unique.values(), key=lambda r: (r['kind'], r['name']))
    report.update({'directory_records': len(records), 'event_detail_records': len(events),
        'archive_event_records': len(archive_events), 'complete': False,
        'limitations': ['Initial official directory/archive extraction; pagination, historical guidebooks, renames and event reconciliation require review.']})
    for name, data in [('directory', records), ('events', events), ('archive_events', archive_events), ('coverage', report)]:
        (out/(name+'.json')).write_text(json.dumps(data, ensure_ascii=False, indent=2)+'\n')
    print(json.dumps({k:v for k,v in report.items() if k not in ('sources',)}, indent=2))
if __name__ == '__main__': main()
