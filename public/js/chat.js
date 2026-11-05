(() => {
    const configNode = document.getElementById('universe-chat-config');
    if (!configNode) return;
    const config = JSON.parse(configNode.textContent), form = document.querySelector('.msger-inputarea');
    const input = form.querySelector('[name="message"]'), box = document.querySelector('.msger-chat'), status = document.getElementById('chat-status');
    const seen = new Set(Array.from(box.querySelectorAll('[data-message-id]'), n => Number(n.dataset.messageId)));
    let after = config.after, before = config.before, busy = false, pending = null, lastRead = 0, stopped = false;
    const headers = {'Accept': 'application/json', 'X-CSRF-Token': config.token};
    function row(message) {
        const node = document.createElement('div'); node.className = 'msg ' + (message.issender ? 'left-msg' : 'right-msg'); node.dataset.messageId = message.id;
        const bubble = document.createElement('div'); bubble.className = 'msg-bubble';
        const info = document.createElement('div'); info.className = 'msg-info';
        const name = document.createElement('div'); name.className = 'msg-info-name'; name.textContent = message.msg.contact.name; info.append(name);
        const text = document.createElement('div'); text.className = 'msg-text'; text.style.whiteSpace = 'pre-wrap'; text.textContent = message.msg.txt;
        const date = document.createElement('p'); date.className = 'small mt-2 mb-1'; date.textContent = new Date(message.msg.contact.datetimeSent).toLocaleString();
        bubble.append(info, text, date); node.append(bubble); return node;
    }
    function append(message) {
        if (seen.has(message.id)) return;
        const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 100;
        seen.add(message.id); box.append(row(message));
        if (atBottom || !message.issender) box.scrollTop = box.scrollHeight;
    }
    async function read() {
        if (after <= lastRead || document.hidden || stopped) return;
        try {
            const response = await fetch(config.readUrl, {method: 'POST', headers: {...headers, 'Content-Type': 'application/json'}, body: JSON.stringify({messageId: after})});
            if (response.ok) { lastRead = after; document.querySelectorAll('#chat-conversations .active .badge').forEach(n => n.remove()); }
        } catch (_) { /* Retry with the next successful poll. */ }
    }
    async function poll() {
        if (busy || document.hidden || stopped) return;
        busy = true;
        try {
            const response = await fetch(config.url + '?after=' + after, {headers, cache: 'no-store'});
            if (response.status === 403 || response.redirected) { stopped = true; input.disabled = true; throw new Error('Sign in again or reopen a conversation you can access.'); }
            if (!response.ok) throw new Error('Connection interrupted. Reconnecting…');
            const messages = await response.json();
            messages.forEach(message => { append(message); after = Math.max(after, message.id); });
            if (!pending) status.textContent = '';
            await read();
        } catch (error) { status.textContent = error.message || 'Connection interrupted. Reconnecting…'; }
        finally { busy = false; }
    }
    form.addEventListener('submit', async event => {
        event.preventDefault(); const value = input.value;
        if (!value.trim() || stopped) return;
        if (!pending || pending.value !== value) pending = {value, id: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(36) + Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2)};
        const button = form.querySelector('button'); button.disabled = true; status.textContent = 'Sending…';
        const body = new FormData(form); body.set('client_id', pending.id);
        try {
            const response = await fetch(config.url, {method: 'POST', headers, body});
            if (response.redirected) throw new Error('Your session ended. Sign in again before sending.');
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Message could not be sent. Retry to send the same message once.');
            append(data); if (input.value === value) input.value = ''; pending = null; status.textContent = ''; await poll();
        } catch (error) { status.textContent = error.message || 'Message could not be sent. Please retry.'; }
        finally { button.disabled = stopped; }
    });
    input.addEventListener('keydown', event => { if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) { event.preventDefault(); form.requestSubmit(); } });
    const older = document.getElementById('chat-load-older');
    if (older) older.addEventListener('click', async () => {
        older.disabled = true;
        try {
            const response = await fetch(config.url + '?before=' + before, {headers, cache: 'no-store'}); if (!response.ok || response.redirected) throw new Error('Older messages could not be loaded.');
            const messages = await response.json(), fragment = document.createDocumentFragment(), height = box.scrollHeight;
            messages.forEach(message => { if (!seen.has(message.id)) { seen.add(message.id); fragment.append(row(message)); } });
            box.prepend(fragment); box.scrollTop += box.scrollHeight - height; if (messages.length) before = messages[0].id;
            if (messages.length < 200) older.hidden = true;
        } catch (error) { status.textContent = error.message; }
        finally { older.disabled = false; }
    });
    box.scrollTop = box.scrollHeight; read(); const timer = setInterval(poll, 3000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
    window.addEventListener('online', poll); window.addEventListener('pagehide', () => { stopped = true; clearInterval(timer); });
})();
