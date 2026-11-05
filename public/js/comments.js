(() => {
    const root = document.getElementById('fos_comment_thread');
    if (!root || root.dataset.bound) return; root.dataset.bound = 'true';
    const url = fos_comment_thread_api_base_url + '/' + encodeURIComponent(fos_comment_thread_id) + '/comments';
    const actionToken = () => JSON.parse(document.getElementById('universe-action-config').textContent).token;
    async function load() { const response = await fetch(url); if(response.ok)root.innerHTML = await response.text(); }
    root.addEventListener('submit', async event => {
        if (!event.target.matches('form')) return; event.preventDefault();
        const form=event.target, button=form.querySelector('[type="submit"]');if(button)button.disabled=true;
        try { const response=await fetch(form.action,{method:'POST',body:new FormData(form)});if(!response.ok)throw new Error('Comment could not be saved. Please try again.');await load(); }
        catch(error){const field=form.querySelector('textarea');if(field){field.setCustomValidity(error.message);field.reportValidity();field.addEventListener('input',()=>field.setCustomValidity(''),{once:true});}}
        finally{if(button)button.disabled=false;}
    });
    root.addEventListener('click', async event => {
        const button=event.target.closest('[data-url],.delete-comment-btn,.fos_comment_comment_reply_cancel,.fos_comment_comment_edit_cancel');if(!button)return;event.preventDefault();
        if(button.matches('.fos_comment_comment_reply_cancel,.fos_comment_comment_edit_cancel')){await load();return;}
        let target=button.dataset.url,method='GET';
        if(button.matches('.delete-comment-btn')){target='/delete-comment/'+button.dataset.commentId;method='POST';}
        if(button.matches('.fos_comment_thread_commentable_action'))method='POST';
        if(button.dataset.parentId)target+='?parentId='+encodeURIComponent(button.dataset.parentId);
        const response=await fetch(target,{method,headers:{'X-CSRF-Token':actionToken()}});if(!response.ok)return;
        if(method==='POST'){await load();return;}
        const holder=document.createElement('div');holder.innerHTML=await response.text();
        if(button.dataset.container)document.querySelector(button.dataset.container).replaceChildren(holder);else button.after(holder);
    });
    load();
})();
