/* Preserve existing links and AJAX interactions while using CSRF-protected POST requests. */
(function () {
    const configElement = document.getElementById('universe-action-config');
    if (!configElement) return;
    const config = JSON.parse(configElement.textContent);
    const paths = config.paths.map(path => new RegExp('^' + path.split(/(\{[^}]+\})/).map(part => part.startsWith('{') ? '[^/]+' : part.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('') + '/?$'));
    function action(url) {
        try { const target = new URL(url, location.href); return target.origin === location.origin && paths.some(path => path.test(target.pathname)); }
        catch (_) { return false; }
    }
    document.addEventListener('click', function (event) {
        const link = event.target.closest('a');
        if (!link || event.defaultPrevented || !action(link.href)) return;
        event.preventDefault();
        const form = document.createElement('form'); form.method = 'post'; form.action = link.href;
        const token = document.createElement('input'); token.type = 'hidden'; token.name = '_action_token'; token.value = config.token;
        form.appendChild(token); document.body.appendChild(form); form.submit();
    });
    document.addEventListener('submit', function (event) {
        if (!action(event.target.action)) return;
        const token = document.createElement('input'); token.type = 'hidden'; token.name = '_action_token'; token.value = config.token;
        event.target.appendChild(token); event.target.method = 'post';
    });
    if (window.jQuery) window.jQuery.ajaxPrefilter(function (options, original, xhr) {
        if (action(options.url)) { options.type = 'POST'; options.method = 'POST'; xhr.setRequestHeader('X-CSRF-Token', config.token); }
    });
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function (input, init) {
            const url = typeof input === 'string' || input instanceof URL ? input : input.url;
            if (!action(url)) return originalFetch.call(this, input, init);
            const headers = new Headers((init && init.headers) || (input instanceof Request ? input.headers : undefined)); headers.set('X-CSRF-Token', config.token);
            return originalFetch.call(this, input, Object.assign({}, init, { method: 'POST', headers: headers }));
        };
    }
})();
