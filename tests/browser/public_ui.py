"""Read-only public-page smoke test. Requires Python Playwright and Chromium.
Run: UNIVERSE_BROWSER=/path/to/chromium python tests/browser/public_ui.py --base-url http://127.0.0.1:18301
Optional screenshots and report: --output /var/tmp/universe-public-check
"""
import argparse
import asyncio
import json
import os
from pathlib import Path
from playwright.async_api import async_playwright

async def check(args):
    reports, errors, failed = [], [], []
    async with async_playwright() as p:
        options = {'headless': True, 'args': ['--no-sandbox']}
        if os.getenv('UNIVERSE_BROWSER'):
            options['executable_path'] = os.environ['UNIVERSE_BROWSER']
        browser = await p.chromium.launch(**options)
        context = await browser.new_context(viewport={'width': 1440, 'height': 1000})
        await context.add_init_script("try { localStorage.setItem('cookiesAccepted','true'); } catch (_) {}")
        page = await context.new_page()
        page.on('pageerror', lambda error: errors.append(str(error)))
        page.on('response', lambda response: failed.append([response.status, response.url]) if response.status >= 400 and response.request.resource_type in ['image', 'script', 'stylesheet', 'media'] else None)
        paths = ['/', '/events', '/creators', '/venues', '/athletics', '/listings', '/blog', '/organizer/organizer-005', '/contact', '/help-center', '/page/about-us', '/page/privacy-policy', '/page/cookie-policy', '/page/legal-notice', '/signup/attendee', '/signup/organizer', '/signin']
        details = []
        if args.output:
            Path(args.output).mkdir(parents=True, exist_ok=True)
        for path in paths:
            response = await page.goto(args.base_url + path, wait_until='networkidle')
            assert response.status == 200, (path, response.status)
            assert await page.locator('h1').count() == 1, path
            if path in ['/events', '/venues', '/athletics', '/listings', '/blog']:
                link = await page.locator('.catalog-grid a').first.get_attribute('href')
                details.append(link)
            for width in [375, 768, 1024, 1440, 1920]:
                await page.set_viewport_size({'width': width, 'height': 1000})
                await page.wait_for_timeout(350)
                assert not await page.evaluate('document.documentElement.scrollWidth > innerWidth + 1'), (path, width)
                if width < 992:
                    assert not await page.locator('#main_nav').is_visible(), (path, 'closed mobile navigation')
                if args.output and width in [375, 1440]:
                    name = (path.strip('/').replace('/', '-') or 'home') + '-' + str(width) + '.png'
                    await page.screenshot(path=str(Path(args.output) / name), full_page=True)
            assert await page.locator('img').evaluate_all('images => images.every(image => !image.complete || image.naturalWidth > 0)'), path
            reports.append({'path': path, 'widths': [375, 768, 1024, 1440, 1920], 'status': 'passed'})
            print('PASSED', path, flush=True)
        for link in details:
            url = link if link.startswith('http') else args.base_url + link
            response = await page.goto(url, wait_until='networkidle')
            assert response.status == 200, url
            assert await page.locator('h1').count() == 1, url
            for width in [375, 768, 1024, 1440, 1920]:
                await page.set_viewport_size({'width': width, 'height': 1000})
                await page.wait_for_timeout(350)
                assert not await page.evaluate('document.documentElement.scrollWidth > innerWidth + 1'), (link, width)
            if args.output:
                await page.screenshot(path=str(Path(args.output) / (link.split('/')[-1] + '.png')), full_page=True)
            print('PASSED detail', link, flush=True)
        assert not errors, errors
        assert not failed, failed
        if args.output:
            (Path(args.output) / 'report.json').write_text(json.dumps({'pages': reports, 'errors': errors, 'failed': failed}, indent=2))
        await browser.close()
        print('All public pages passed; no failed media, script, or stylesheet requests.', flush=True)

if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--base-url', required=True)
    parser.add_argument('--output')
    arguments = parser.parse_args()
    arguments.base_url = arguments.base_url.rstrip('/')
    asyncio.run(check(arguments))
