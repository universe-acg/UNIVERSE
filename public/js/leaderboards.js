(() => {
    const root=document.querySelector('.main'), input=document.getElementById('filter-input'), description=document.getElementById('description-text'), status=document.getElementById('leaderboard-status');
    if(!root||!input)return;
    let tab='creators',level='';const normalized=s=>s.trim().toLowerCase().replace(/^[-\s]+/,'');
    const creators=document.getElementById('creators-content'),attendees=document.getElementById('attendees-content');
    function filter(){
        const needle=input.value.trim().toLowerCase();let shown=0;
        for(const panel of [creators,attendees]){
            const active=panel.id===tab+'-content';panel.hidden=!active;panel.style.display=active?'block':'none';
            panel.querySelectorAll('tbody tr').forEach(row=>{
                const match=(!needle||row.textContent.toLowerCase().includes(needle))&&(!level||normalized(row.querySelector('.points-level')?.textContent||'')===level);
                row.hidden=!match;row.style.display=match?'table-row':'none';if(match&&active)shown++;
            });
        }
        document.getElementById('creators-tab').setAttribute('aria-pressed',String(tab==='creators'));document.getElementById('attendees-tab').setAttribute('aria-pressed',String(tab==='attendees'));
        description.textContent=level?'Level: '+level:'';status.textContent=shown?shown+' '+(tab==='creators'?'creators':'attendees')+' shown.':'No matching participants. Try another search or choose All.';
    }
    for(const kind of ['creators','attendees'])document.getElementById(kind+'-tab').addEventListener('click',()=>{tab=kind;level='';filter();});
    input.addEventListener('input',filter);
    document.getElementById('show-all-button').setAttribute('href','#');document.getElementById('show-all-button').addEventListener('click',event=>{event.preventDefault();input.value='';level='';filter();});
    root.querySelectorAll('.creators-tags a,.attendees-tags a').forEach(link=>{link.href='#';link.addEventListener('click',event=>{event.preventDefault();tab=link.closest('.attendees-tags')?'attendees':'creators';level=normalized(link.textContent);filter();});});
    const tag=new URL(location.href).searchParams.get('tag');
    if(tag){const match=Array.from(root.querySelectorAll('.creators-tags a,.attendees-tags a')).find(link=>normalized(link.textContent)===normalized(tag));if(match){tab=match.closest('.attendees-tags')?'attendees':'creators';level=normalized(tag);}}
    filter();
})();
