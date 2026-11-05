(() => {
    const config=JSON.parse(document.getElementById('reels-config').textContent), grid=document.getElementById('reels-container'), modal=document.getElementById('reel-modal');
    const image=document.getElementById('reel-modal-image'), video=document.getElementById('reel-modal-video'), load=document.getElementById('reels-load-more'), status=document.getElementById('reels-status');
    let current=-1, returnFocus=null, page=config.page, loading=false, more=config.hasMore;
    const cards=()=>Array.from(grid.querySelectorAll('[data-reel-open]'));
    function show(index){
        const items=cards();if(!items.length)return;current=(index+items.length)%items.length;const item=items[current];
        video.pause();video.removeAttribute('src');video.load();video.hidden=!item.dataset.video;image.hidden=!!item.dataset.video;
        document.getElementById('reel-modal-title').textContent=item.dataset.title;document.getElementById('reel-modal-caption').textContent=item.dataset.caption;document.getElementById('reel-modal-details').href=item.href;
        if(item.dataset.video){video.src=item.dataset.video;video.poster=item.dataset.image;}else{image.src=item.dataset.image;image.alt=item.dataset.title;}
        modal.hidden=false;document.body.style.overflow='hidden';document.getElementById('close-carousel').focus();
    }
    function close(){video.pause();video.removeAttribute('src');video.load();modal.hidden=true;document.body.style.overflow='';if(returnFocus)returnFocus.focus();}
    grid.addEventListener('click',event=>{const card=event.target.closest('[data-reel-open]');if(!card)return;event.preventDefault();returnFocus=card;show(cards().indexOf(card));});
    document.getElementById('close-carousel').addEventListener('click',close);document.getElementById('prev-card').addEventListener('click',()=>show(current-1));document.getElementById('next-card').addEventListener('click',()=>show(current+1));
    modal.addEventListener('click',event=>{if(event.target===modal||event.target.classList.contains('centered-container'))close();});
    document.addEventListener('keydown',event=>{
        if(modal.hidden)return;
        if(event.key==='Escape')close();
        if(event.key==='ArrowRight')show(current+1);if(event.key==='ArrowLeft')show(current-1);
        if(event.key==='Tab'){
            const focusable=Array.from(modal.querySelectorAll('button,a,video[controls]')).filter(n=>!n.hidden&&n.offsetParent!==null), first=focusable[0],last=focusable[focusable.length-1];
            if(event.shiftKey&&document.activeElement===first){event.preventDefault();last.focus();}else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first.focus();}
        }
    });
    async function next(){
        if(loading||!more)return;loading=true;load.disabled=true;status.textContent='Loading reels…';
        try{
            const url=new URL(config.url,location.origin);url.searchParams.set('page',page+1);if(config.keyword)url.searchParams.set('keyword',config.keyword);
            const response=await fetch(url,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error('Could not load more reels. Try again.');const data=await response.json();
            const fragment=document.createElement('template');fragment.innerHTML=data.html;const seen=new Set(cards().map(n=>n.dataset.reelId));
            fragment.content.querySelectorAll('[data-reel-id]').forEach(node=>{if(seen.has(node.dataset.reelId))node.remove();});grid.append(fragment.content);page=data.page;more=data.nextPage!==null;load.hidden=!more;status.textContent=more?'':'All reels loaded.';
        }catch(error){status.textContent=error.message;}finally{loading=false;load.disabled=false;}
    }
    load.addEventListener('click',next);
})();
