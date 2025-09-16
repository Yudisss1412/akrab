/* ======= Dummy data (bisa di-API-kan nanti) ======= */
const data = {
  user: { name: "yudistiradwianggara" },
  belumDinilai: [
    {
      id: "b1",
      timeISO: "2025-08-29T19:50:00+07:00",
      product: {
        title: "Kaki Kursi Kantor 280 Tanpa Roda (Baru)",
        variant: "",
        url: "#"
      }
    }
  ],
  dinilai: [
    {
      id: "r1",
      timeISO: "2025-07-19T15:37:00+07:00",
      rating: 5,
      kv: [
        ["Fungsi", "beroprasi dengan lancar"],
        ["Fitur", "bagus dan berguna"],
        ["Desain", "menarik dan modern"],
      ],
      product: {
        title: "【MG】 Stick Stik DS4 LightBar + DUS ASLI",
        variant: "Variasi: Hitam, SAMA BOX + KABEL",
        url: "#"
      }
    },
    {
      id: "r2",
      timeISO: "2024-12-04T19:39:00+07:00",
      rating: 5,
      kv: [],
      product: {
        title: "Gedi Luxury 13011 Jam Tangan Wanita Rantai",
        variant: "Variasi: Full Black",
        url: "#"
      }
    }
  ],
  stats: { ulasan: 16, koin: 25, terbantu: 0, dilihat: 0 }
};

/* ======= Utilities ======= */
const $ = (sel, el = document) => el.querySelector(sel);
const $$ = (sel, el = document) => Array.from(el.querySelectorAll(sel));

function formatDate(iso){
  // Contoh: 29-08-2025 19:50
  const d = new Date(iso);
  const pad = n => n.toString().padStart(2,'0');
  const dd = pad(d.getDate());
  const mm = pad(d.getMonth()+1);
  const yyyy = d.getFullYear();
  const hh = pad(d.getHours());
  const mi = pad(d.getMinutes());
  return `${dd}-${mm}-${yyyy} ${hh}:${mi}`;
}

function makeStars(n=0){
  const wrap = document.createElement('div');
  wrap.className = 'stars';
  for(let i=1;i<=5;i++){
    const span = document.createElement('span');
    span.className = 'star';
    span.innerHTML = `<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
      <path d="M12 .587l3.668 7.431 8.2 1.192-5.934 5.788 1.402 8.168L12 18.896l-7.336 3.87 1.402-8.168L.132 9.21l8.2-1.192L12 .587z"
        fill="${i<=n ? '#f6c34f' : '#3a4157'}"/></svg>`;
    wrap.appendChild(span);
  }
  return wrap;
}

function addKV(ul, kvPairs){
  kvPairs.forEach(([k,v])=>{
    const li = document.createElement('li');
    li.innerHTML = `<span class="k">${k}:</span><span class="v">${v}</span>`;
    ul.appendChild(li);
  });
}

/* ======= Renderers ======= */
function renderStats(){
  $('#statUlasan').textContent = data.stats.ulasan;
  $('#statTerbantu').textContent = data.stats.terbantu;
  $('#statDilihat').textContent  = data.stats.dilihat;
}

function renderList(container, items, {withRating}={}){
  container.innerHTML = '';
  if(!items || items.length===0){
    const empty = document.importNode($('#tplEmpty').content, true);
    container.appendChild(empty);
    return;
  }

  items.forEach(item=>{
    const card = document.importNode($('#tplCard').content, true);
    const art = card.querySelector('.card');
    art.dataset.id = item.id;

    // Header
    card.querySelector('.user__name').textContent = data.user.name;
    const starsWrap = card.querySelector('.stars');
    if(withRating){
      starsWrap.replaceWith(makeStars(item.rating || 0));
    }else{
      starsWrap.remove(); // belum dinilai => tidak ada bintang
    }
    const timeEl = card.querySelector('.card__time');
    timeEl.dateTime = item.timeISO;
    timeEl.textContent = formatDate(item.timeISO);

    // Body
    const kvUL = card.querySelector('.kv');
    if(withRating && item.kv && item.kv.length){
      addKV(kvUL, item.kv);
    }else{
      kvUL.remove();
    }

    const p = item.product || {};
    card.querySelector('.product').href = p.url || '#';
    card.querySelector('.product__title').textContent = p.title || 'Produk';
    card.querySelector('.product__variant').textContent = p.variant || '';

    // Footer buttons
    if(!withRating){
      // Belum Dinilai: tombol "Tulis Ulasan" alih-alih Perbarui
      const upd = card.querySelector('.btn-update');
      upd.textContent = 'Tulis Ulasan';
      upd.addEventListener('click', ()=> alert('Aksi: Tulis Ulasan untuk item '+(p.title||'')));
    }else{
      card.querySelector('.btn-update').addEventListener('click', ()=> alert('Aksi: Perbarui ulasan '+(p.title||'')));
    }
    card.querySelector('.btn-help').addEventListener('click', (e)=>{
      e.currentTarget.classList.toggle('is-on');
      if(e.currentTarget.classList.contains('is-on')){
        e.currentTarget.style.borderColor = 'var(--ok)';
        e.currentTarget.style.color = 'var(--ok)';
      }else{
        e.currentTarget.style.borderColor = 'var(--line)';
        e.currentTarget.style.color = 'var(--text)';
      }
    });

    container.appendChild(card);
  });
}

/* ======= Tabs & Toolbar ======= */
function setupTabs(){
  const tabs = $$('.tab');
  const indicator = $('.tab__indicator');
  const listBelum = $('#listBelum');
  const listDinilai = $('#listDinilai');

  function activate(key){
    tabs.forEach(t=>{
      const active = t.dataset.tab === key;
      t.classList.toggle('is-active', active);
      t.setAttribute('aria-selected', String(active));
    });
    if(key==='belum'){
      listBelum.hidden = false; listDinilai.hidden = true;
    }else{
      listBelum.hidden = true; listDinilai.hidden = false;
    }

    // Pindah indikator
    const activeTab = $('.tab.is-active');
    const rect = activeTab.getBoundingClientRect();
    const host = $('.tabs').getBoundingClientRect();
    indicator.style.width = rect.width + 'px';
    indicator.style.left = (activeTab.offsetLeft + 14) + 'px';
  }

  tabs.forEach(t=>t.addEventListener('click', ()=>activate(t.dataset.tab)));
  window.addEventListener('resize', ()=>activate($('.tab.is-active').dataset.tab));

  // init
  activate('dinilai');
}

function setupSearchAndSort(){
  const input = $('#searchInput');
  const select = $('#sortSelect');

  function apply(){
    const q = input.value.trim().toLowerCase();
    let list = [...data.dinilai];

    if(q){
      list = list.filter(it=>{
        const hay = [it.product?.title, it.product?.variant, ...(it.kv||[]).flat()].join(' ').toLowerCase();
        return hay.includes(q);
      });
    }

    switch(select.value){
      case 'oldest':
        list.sort((a,b)=> new Date(a.timeISO) - new Date(b.timeISO)); break;
      case 'ratingHigh':
        list.sort((a,b)=> (b.rating||0) - (a.rating||0)); break;
      case 'ratingLow':
        list.sort((a,b)=> (a.rating||0) - (b.rating||0)); break;
      default: // latest
        list.sort((a,b)=> new Date(b.timeISO) - new Date(a.timeISO));
    }

    renderList($('#listDinilai'), list, {withRating:true});
  }

  input.addEventListener('input', apply);
  select.addEventListener('change', apply);

  // initial for dinilai
  apply();
}

/* ======= Init ======= */
document.addEventListener('DOMContentLoaded', ()=>{
  renderStats();
  renderList($('#listBelum'), data.belumDinilai, {withRating:false});
  setupTabs();
  setupSearchAndSort();

  // back button (opsional: ganti URL sesuai app kamu)
  $('#btnBack').addEventListener('click', ()=> history.length>1 ? history.back() : location.href = './profil_pembeli.html');
});
