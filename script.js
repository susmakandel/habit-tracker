const DEMO_HABITS = [
  {id:1,name:'Drink 8 Glasses of Water',icon:'💧',color:'#3B82F6',frequency:'daily',streak:5,completed:true},
  {id:2,name:'Morning Exercise',icon:'🏃',color:'#F59E0B',frequency:'daily',streak:3,completed:true},
  {id:3,name:'Read Book',icon:'📗',color:'#10B981',frequency:'daily',streak:7,completed:true},
  {id:4,name:'Learn Something New',icon:'🧠',color:'#8B5CF6',frequency:'daily',streak:2,completed:false},
  {id:5,name:'Meditation',icon:'🧘',color:'#EF4444',frequency:'daily',streak:4,completed:true},
];
const WEEK_DAYS=['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
const DEMO_WEEK=[4,5,3,6,5,2,5];
let habits=[], selectedColor='#7C3AED';

document.addEventListener('DOMContentLoaded',()=>{
  habits=JSON.parse(JSON.stringify(DEMO_HABITS));
  renderHabits(); renderWeeklyChart(DEMO_WEEK,85,5,7); bindUI();
});

function renderHabits(){
  const list=document.getElementById('habitsList'); list.innerHTML='';
  habits.forEach(h=>{
    const li=document.createElement('li');
    li.className='habit-item'+(h.completed?' completed':'');
    li.innerHTML=`
      <div class="habit-icon-wrap" style="background:${h.color}22">${h.icon||'⭐'}</div>
      <div class="habit-info">
        <div class="habit-name">${h.name}</div>
        <div class="habit-meta">${h.frequency.charAt(0).toUpperCase()+h.frequency.slice(1)} · Streak ${h.streak}</div>
      </div>
      <div class="habit-checkbox ${h.completed?'checked':''}" data-id="${h.id}"></div>
      <button class="habit-delete" data-id="${h.id}">✕</button>`;
    list.appendChild(li);
  });
  document.querySelectorAll('.habit-checkbox').forEach(el=>el.addEventListener('click',()=>toggle(+el.dataset.id)));
  document.querySelectorAll('.habit-delete').forEach(el=>el.addEventListener('click',()=>del(+el.dataset.id)));
}

function toggle(id){
  const h=habits.find(x=>x.id===id); if(!h) return;
  h.completed=!h.completed; h.streak+=h.completed?1:-1;
  renderHabits(); refreshStats();
  toast(h.completed?'✅ Habit complete!':'↩ Habit unchecked');
}

function del(id){
  if(!confirm('Remove this habit?')) return;
  habits=habits.filter(h=>h.id!==id); renderHabits(); refreshStats(); toast('🗑 Habit removed');
}

function addHabit(){
  const name=document.getElementById('habitName').value.trim();
  const freq=document.getElementById('habitFrequency').value;
  if(!name){toast('⚠️ Enter a habit name');return;}
  const icons=['⭐','🎯','🚀','💡','🎨','🏆','🔥','💎'];
  habits.push({id:Date.now(),name,icon:icons[Math.floor(Math.random()*icons.length)],color:selectedColor,frequency:freq,streak:0,completed:false});
  renderHabits(); refreshStats(); closeModal(); toast('🎉 Habit added!');
}

function refreshStats(){
  const done=habits.filter(h=>h.completed).length, total=habits.length;
  const streak=total?Math.max(...habits.map(h=>h.streak)):0;
  const pct=total?Math.round((done/total)*100):0;
  document.getElementById('totalHabits').textContent=total;
  document.getElementById('completedToday').textContent=done;
  document.getElementById('currentStreak').textContent=streak;
  document.getElementById('successRate').textContent=pct+'%';
  const w=[...DEMO_WEEK]; w[6]=done;
  renderWeeklyChart(w,pct,done,total);
}

function renderWeeklyChart(counts,pct,done,total){
  const c=314.16, filled=(pct/100)*c;
  document.getElementById('donutFill').style.strokeDasharray=`${filled} ${c}`;
  document.getElementById('donutPct').textContent=pct+'%';
  document.getElementById('weeklyFraction').textContent=done+'/'+total;
  const container=document.getElementById('weeklyBarChart'); container.innerHTML='';
  const maxV=Math.max(...counts,1);
  counts.forEach((v,i)=>{
    const col=document.createElement('div'); col.className='bar-col';
    const bar=document.createElement('div'); bar.className='bar'+(i===counts.length-1?' today-bar':'');
    bar.style.height=Math.max(4,Math.round((v/maxV)*50))+'px';
    const lbl=document.createElement('span'); lbl.className='bar-label'; lbl.textContent=WEEK_DAYS[i];
    col.append(bar,lbl); container.appendChild(col);
  });
}

function openModal(){document.getElementById('modalOverlay').classList.add('open');}
function closeModal(){document.getElementById('modalOverlay').classList.remove('open');document.getElementById('habitName').value='';}
function toast(msg){const t=document.createElement('div');t.className='toast';t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.remove(),2900);}

function bindUI(){
  document.getElementById('openAddModal').addEventListener('click',openModal);
  document.getElementById('closeModal').addEventListener('click',closeModal);
  document.getElementById('cancelModal').addEventListener('click',closeModal);
  document.getElementById('saveHabit').addEventListener('click',addHabit);
  document.getElementById('modalOverlay').addEventListener('click',e=>{if(e.target.id==='modalOverlay')closeModal();});
  document.getElementById('habitName').addEventListener('keydown',e=>{if(e.key==='Enter')addHabit();});
  document.querySelectorAll('.color-dot').forEach(d=>d.addEventListener('click',()=>{
    document.querySelectorAll('.color-dot').forEach(x=>x.classList.remove('selected'));
    d.classList.add('selected'); selectedColor=d.dataset.color;
  }));
  document.querySelectorAll('.nav-item[data-page]').forEach(item=>item.addEventListener('click',e=>{
    e.preventDefault();
    document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
    item.classList.add('active');
  }));
  document.getElementById('logoutBtn').addEventListener('click',e=>{e.preventDefault();toast('👋 Logged out');});
}
