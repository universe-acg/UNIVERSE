(() => {
    const button=document.getElementById('reel-like');if(!button)return;
    button.addEventListener('click',async()=>{
        button.disabled=true;
        try{
            const token=JSON.parse(document.getElementById('universe-action-config').textContent).token;
            const response=await fetch(button.dataset.url,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-Token':token,'Accept':'application/json'},body:JSON.stringify({liked:button.getAttribute('aria-pressed')!=='true'})});
            if(!response.ok||response.redirected)throw new Error('Like could not be saved. Please sign in and retry.');
            const data=await response.json();button.setAttribute('aria-pressed',String(data.liked));button.textContent=data.liked?'Unlike':'Like';document.getElementById('reel-like-count').textContent=data.count;
        }catch(error){document.getElementById('reel-like-status').textContent=error.message;}finally{button.disabled=false;}
    });
})();
