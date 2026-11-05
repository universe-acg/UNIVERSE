/* Shared public-page behavior; existing action endpoints retain their CSRF checks. */
document.addEventListener('DOMContentLoaded', function () {
    if (!document.body.classList.contains('universe-public')) return;
    function fallback(image) {
        if (image.dataset.fallbackApplied) return;
        image.dataset.fallbackApplied = 'true';
        image.removeAttribute('srcset');
        image.classList.remove('img-lazy-load');
        image.src = '/images/universe-placeholder.svg';
        image.closest('.img-wrap')?.querySelector('.loader')?.remove();
    }
    document.addEventListener('error', function (event) {
        if (event.target instanceof HTMLImageElement) fallback(event.target);
    }, true);
    document.querySelectorAll('img').forEach(function (image) {
        if (image.complete && !image.naturalWidth) fallback(image);
    });

    document.querySelectorAll('.catalog-filters').forEach(function (panel) {
        var toggle = document.createElement('button');
        toggle.type = 'button'; toggle.className = 'btn btn-outline-primary catalog-toggle';
        toggle.innerHTML = 'Search and filters <span aria-hidden="true">⌄</span>';
        toggle.setAttribute('aria-expanded', 'false');
        panel.dataset.collapsed = 'true';
        // Hide only after JS is available; forms remain usable without JS.
        toggle.addEventListener('click', function () {
            var expanded = toggle.getAttribute('aria-expanded') !== 'true';
            toggle.setAttribute('aria-expanded', String(expanded));
            panel.dataset.collapsed = String(!expanded);
        });
        panel.prepend(toggle);
        var controls = document.createElement('div'); controls.id = 'catalog-filter-controls';
        Array.from(panel.children).forEach(function (child) { if (child !== toggle) controls.append(child); });
        panel.append(controls); toggle.setAttribute('aria-controls', controls.id);
        panel.querySelectorAll('input:not([type=hidden]),select').forEach(function (field) {
            if (!field.id) field.id = 'catalog-' + field.name;
            if (!field.labels?.length && !field.getAttribute('aria-label')) {
                field.setAttribute('aria-label', field.closest('article')?.querySelector('.title')?.textContent.trim() || field.name);
            }
        });
    });

    var tabs = document.querySelector('.profile-tabs');
    if (tabs) {
        var links = Array.from(tabs.querySelectorAll('[role=tab]'));
        function selectTab(link) {
            links.forEach(function (item) {
                var selected = item === link;
                item.classList.toggle('active', selected);
                item.setAttribute('aria-selected', String(selected));
                item.tabIndex = selected ? 0 : -1;
                var pane = document.querySelector(item.getAttribute('href'));
                if (pane) { pane.classList.toggle('active', selected); pane.classList.toggle('show', selected); }
            });
        }
        tabs.addEventListener('click', function (event) {
            var link = event.target.closest('[role=tab]');
            if (!link) return;
            event.preventDefault(); selectTab(link); history.replaceState(null, '', link.getAttribute('href'));
        });
        tabs.addEventListener('keydown', function (event) {
            var index = links.indexOf(document.activeElement);
            if (index < 0) return;
            var next;
            if (event.key === 'ArrowRight') next = (index + 1) % links.length;
            if (event.key === 'ArrowLeft') next = (index + links.length - 1) % links.length;
            if (event.key === 'Home') next = 0;
            if (event.key === 'End') next = links.length - 1;
            if (next === undefined) return;
            event.preventDefault(); selectTab(links[next]); links[next].focus();
            history.replaceState(null, '', links[next].getAttribute('href'));
        });
        function fromHash() { selectTab(links.find(function (link) { return link.getAttribute('href') === location.hash; }) || links[0]); }
        window.addEventListener('hashchange', fromHash); fromHash();
    }

    document.querySelectorAll('.auth-panel input').forEach(function (input) {
        if (!input.id || input.type === 'hidden' || input.type === 'checkbox') return;
        if (!input.labels.length) {
            var label = document.createElement('label'); label.htmlFor = input.id;
            label.textContent = input.placeholder || input.name.split('[').pop().replace(']', '').replace(/([A-Z])/g, ' $1');
            if (input.name.includes('[first]') && input.type === 'password') label.textContent = 'Password';
            if (input.name.includes('[second]')) label.textContent = 'Confirm password';
            label.textContent = label.textContent.charAt(0).toUpperCase() + label.textContent.slice(1);
            (input.closest('.input-group') || input).before(label);
        }
        if (input.type === 'password') {
            var button = document.createElement('button');
            button.type = 'button'; button.className = 'btn btn-outline-primary password-toggle';
            button.textContent = 'Show'; button.setAttribute('aria-label', 'Show ' + (input.labels[0]?.textContent || 'password').toLowerCase());
            button.setAttribute('aria-pressed', 'false');
            button.addEventListener('click', function () {
                var visible = input.type === 'password'; input.type = visible ? 'text' : 'password';
                button.textContent = visible ? 'Hide' : 'Show'; button.setAttribute('aria-pressed', String(visible));
                button.setAttribute('aria-label', (visible ? 'Hide ' : 'Show ') + (input.labels[0]?.textContent || 'password').toLowerCase());
            });
            var wrapper = document.createElement('div'); wrapper.className = 'password-field';
            input.before(wrapper); wrapper.append(input, button);
        }
    });
});
