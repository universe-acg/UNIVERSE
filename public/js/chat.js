(() => {
    const config = JSON.parse(document.getElementById('universe-chat-config').textContent);
    const form = document.querySelector('.msger-inputarea'), input = form.querySelector('[name="message"]'), box = document.querySelector('.msger-chat');
    let after = config.after, busy = false;
    function append(message) {
        if (message.id <= after) return;
        after = message.id;
        const row = document.createElement('div'); row.className = 'msg ' + (message.issender ? 'left-msg' : 'right-msg');
        const bubble = document.createElement('div'); bubble.className = 'msg-bubble';
        const info = document.createElement('div'); info.className = 'msg-info';
        const name = document.createElement('div'); name.className = 'msg-info-name'; name.textContent = message.msg.contact.name;
        info.append(name); bubble.append(info);
        const text = document.createElement('div'); text.className = 'msg-text'; text.textContent = message.msg.txt; bubble.append(text);
        const date = document.createElement('p'); date.className = 'text-muted mt-4'; date.style.fontSize = '10px'; date.textContent = new Date(message.msg.contact.datetimeSent).toLocaleString(); bubble.append(date);
        row.append(bubble); box.append(row); box.scrollTop = box.scrollHeight;
    }
    async function poll() {
        if (busy || document.hidden) return;
        busy = true;
        try { const response = await fetch(config.url + '?after=' + after, {headers: {'Accept':'application/json'}}); if(response.ok)(await response.json()).forEach(append); } catch (_) {} finally { busy = false; }
    }
    form.addEventListener('submit', async event => {
        event.preventDefault(); if (!input.value.trim()) return;
        const button = form.querySelector('button'); button.disabled = true;
        try { const response = await fetch(config.url, {method:'POST', body:new FormData(form)}); if(!response.ok)throw new Error('Message could not be sent. Please try again.'); input.value=''; await poll(); }
        catch(error) { input.setCustomValidity(error.message); input.reportValidity(); }
        finally { button.disabled=false; }
    });
    input.addEventListener('input',()=>input.setCustomValidity(''));
    box.scrollTop=box.scrollHeight;setInterval(poll,4000);
})();
