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
const $ = (sel, el = document) => Array.from(el.querySelectorAll(sel));

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
  // Tidak ada elemen statUlasan, statTerbantu, statDilihat dalam HTML
  // Jadi kita lewati bagian ini
}

function createReviewCard(item, withRating = false) {
  const card = document.createElement('div');
  card.className = 'card';
  card.dataset.id = item.id;

  // Header
  const header = document.createElement('div');
  header.className = 'card__head';
  
  const userInfo = document.createElement('div');
  userInfo.className = 'user';
  
  const userName = document.createElement('span');
  userName.className = 'user__name';
  userName.textContent = data.user.name;
  userInfo.appendChild(userName);
  
  header.appendChild(userInfo);
  
  if (withRating) {
    const starsWrap = makeStars(item.rating || 0);
    header.appendChild(starsWrap);
  }
  
  const timeEl = document.createElement('time');
  timeEl.className = 'card__time';
  timeEl.dateTime = item.timeISO;
  timeEl.textContent = formatDate(item.timeISO);
  header.appendChild(timeEl);
  
  card.appendChild(header);

  // Body
  const body = document.createElement('div');
  body.className = 'card__body';
  
  if (withRating && item.kv && item.kv.length) {
    const kvUL = document.createElement('ul');
    kvUL.className = 'kv';
    addKV(kvUL, item.kv);
    body.appendChild(kvUL);
  }
  
  const productLink = document.createElement('a');
  productLink.className = 'product';
  productLink.href = item.product?.url || '#';
  
  const productTitle = document.createElement('div');
  productTitle.className = 'product__title';
  productTitle.textContent = item.product?.title || 'Produk';
  productLink.appendChild(productTitle);
  
  if (item.product?.variant) {
    const productVariant = document.createElement('div');
    productVariant.className = 'product__variant';
    productVariant.textContent = item.product.variant;
    productLink.appendChild(productVariant);
  }
  
  body.appendChild(productLink);
  card.appendChild(body);

  // Footer
  const footer = document.createElement('div');
  footer.className = 'card__foot';
  
  const helpBtn = document.createElement('button');
  helpBtn.className = 'btn';
  helpBtn.textContent = 'Bantu';
  helpBtn.addEventListener('click', (e)=>{
    e.currentTarget.classList.toggle('is-on');
    if(e.currentTarget.classList.contains('is-on')){
      e.currentTarget.style.borderColor = 'var(--ok)';
      e.currentTarget.style.color = 'var(--ok)';
    }else{
      e.currentTarget.style.borderColor = 'var(--border)';
      e.currentTarget.style.color = 'var(--text)';
    }
  });
  footer.appendChild(helpBtn);
  
  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn primary';
  updateBtn.textContent = withRating ? 'Perbarui' : 'Tulis Ulasan';
  updateBtn.addEventListener('click', ()=> {
    if (withRating) {
      alert('Aksi: Perbarui ulasan ' + (item.product?.title || ''));
    } else {
      alert('Aksi: Tulis Ulasan untuk item ' + (item.product?.title || ''));
    }
  });
  footer.appendChild(updateBtn);
  
  card.appendChild(footer);

  return card;
}

function renderList(container, items, {withRating}={}){
  container.innerHTML = '';
  if(!items || items.length===0){
    // Tampilkan empty state
    const parentSection = container.closest('.tab-content');
    if (parentSection) {
      const emptyState = parentSection.querySelector('.empty-state');
      if (emptyState) {
        emptyState.hidden = false;
      }
    }
    return;
  }

  // Sembunyikan empty state
  const parentSection = container.closest('.tab-content');
  if (parentSection) {
    const emptyState = parentSection.querySelector('.empty-state');
    if (emptyState) {
      emptyState.hidden = true;
    }
  }

  items.forEach(item=>{
    const card = createReviewCard(item, withRating);
    container.appendChild(card);
  });
}

/* ======= Tabs & Toolbar ======= */
function setupTabs(){
  const tabs = $('.tab');

  function activate(tabElement){
    // Remove active class from all tabs
    $('.tab').forEach(t => t.classList.remove('active'));
    
    // Add active class to clicked tab
    tabElement.classList.add('active');
    
    // Hide all tab contents
    const tabContents = $('.tab-content');
    tabContents.forEach(content => {
      content.classList.remove('active');
      content.hidden = true;
    });
    
    // Show appropriate content based on tab
    const tabName = tabElement.dataset.tab;
    const targetContent = $(`#${tabName}-content`);
    if(targetContent) {
      targetContent.classList.add('active');
      targetContent.hidden = false;
    }
  }

  tabs.forEach(t => t.addEventListener('click', () => activate(t)));

  // init - activate first tab
  if(tabs.length > 0) {
    activate(tabs[0]);
  }
}

/* ======= Init ======= */
document.addEventListener('DOMContentLoaded', ()=>{
  // Setup tabs
  setupTabs();
  
  // Render review list
  const reviewListContainer = $('#reviewList');
  if(reviewListContainer) {
    renderList(reviewListContainer, data.dinilai, {withRating:true});
  }

  // back button
  const backBtn = $('#btnBack');
  if(backBtn) {
    backBtn.addEventListener('click', ()=> {
      if(history.length > 1) {
        history.back();
      } else {
        window.location.href = '/';
      }
    });
  }
});
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
  // Tidak ada elemen statUlasan, statTerbantu, statDilihat dalam HTML
  // Jadi kita lewati bagian ini
}

function createReviewCard(item, withRating = false) {
  const card = document.createElement('div');
  card.className = 'card';
  card.dataset.id = item.id;

  // Header
  const header = document.createElement('div');
  header.className = 'card__head';
  
  const userInfo = document.createElement('div');
  userInfo.className = 'user';
  
  const userName = document.createElement('span');
  userName.className = 'user__name';
  userName.textContent = data.user.name;
  userInfo.appendChild(userName);
  
  header.appendChild(userInfo);
  
  if (withRating) {
    const starsWrap = makeStars(item.rating || 0);
    header.appendChild(starsWrap);
  }
  
  const timeEl = document.createElement('time');
  timeEl.className = 'card__time';
  timeEl.dateTime = item.timeISO;
  timeEl.textContent = formatDate(item.timeISO);
  header.appendChild(timeEl);
  
  card.appendChild(header);

  // Body
  const body = document.createElement('div');
  body.className = 'card__body';
  
  if (withRating && item.kv && item.kv.length) {
    const kvUL = document.createElement('ul');
    kvUL.className = 'kv';
    addKV(kvUL, item.kv);
    body.appendChild(kvUL);
  }
  
  const productLink = document.createElement('a');
  productLink.className = 'product';
  productLink.href = item.product?.url || '#';
  
  const productTitle = document.createElement('div');
  productTitle.className = 'product__title';
  productTitle.textContent = item.product?.title || 'Produk';
  productLink.appendChild(productTitle);
  
  if (item.product?.variant) {
    const productVariant = document.createElement('div');
    productVariant.className = 'product__variant';
    productVariant.textContent = item.product.variant;
    productLink.appendChild(productVariant);
  }
  
  body.appendChild(productLink);
  card.appendChild(body);

  // Footer
  const footer = document.createElement('div');
  footer.className = 'card__foot';
  
  const helpBtn = document.createElement('button');
  helpBtn.className = 'btn btn-help';
  helpBtn.textContent = 'Bantu';
  helpBtn.addEventListener('click', (e)=>{
    e.currentTarget.classList.toggle('is-on');
    if(e.currentTarget.classList.contains('is-on')){
      e.currentTarget.style.borderColor = 'var(--ok)';
      e.currentTarget.style.color = 'var(--ok)';
    }else{
      e.currentTarget.style.borderColor = 'var(--border)';
      e.currentTarget.style.color = 'var(--text)';
    }
  });
  footer.appendChild(helpBtn);
  
  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn btn-update';
  updateBtn.textContent = withRating ? 'Perbarui' : 'Tulis Ulasan';
  updateBtn.addEventListener('click', ()=> {
    if (withRating) {
      alert('Aksi: Perbarui ulasan ' + (item.product?.title || ''));
    } else {
      alert('Aksi: Tulis Ulasan untuk item ' + (item.product?.title || ''));
    }
  });
  footer.appendChild(updateBtn);
  
  card.appendChild(footer);

  return card;
}

function renderList(container, items, {withRating}={}){
  container.innerHTML = '';
  if(!items || items.length===0){
    // Tampilkan empty state
    const emptyState = container.closest('section').querySelector('.empty-state');
    if (emptyState) {
      emptyState.hidden = false;
    }
    return;
  }

  // Sembunyikan empty state
  const emptyState = container.closest('section').querySelector('.empty-state');
  if (emptyState) {
    emptyState.hidden = true;
  }

  items.forEach(item=>{
    const card = createReviewCard(item, withRating);
    container.appendChild(card);
  });
}

/* ======= Tabs & Toolbar ======= */
function setupTabs(){
  const tabs = $('.tab');
  const ulasanContent = $('#ulasan-content');
  const wishlistContent = $('#wishlist-content');

  function activate(key){
    tabs.forEach(t=>{
      const active = t.dataset.tab === key;
      t.classList.toggle('active', active);
      t.setAttribute('aria-selected', String(active));
    });
    
    if(key === 'ulasan'){
      ulasanContent.classList.add('active');
      wishlistContent.classList.remove('active');
      wishlistContent.hidden = true;
      ulasanContent.hidden = false;
    }else{
      ulasanContent.classList.remove('active');
      wishlistContent.classList.add('active');
      wishlistContent.hidden = false;
      ulasanContent.hidden = true;
    }
  }

  tabs.forEach(t=>t.addEventListener('click', ()=>activate(t.dataset.tab)));
  window.addEventListener('resize', ()=>activate($('.tab.active').dataset.tab));

  // init
  activate('ulasan');
}

function setupSearchAndSort(){
  // Untuk saat ini kita tidak mengimplementasikan search dan sort
  // karena tidak ada elemen input yang sesuai dalam HTML
}

/* ======= Init ======= */
document.addEventListener('DOMContentLoaded', ()=>{
  renderStats();
  renderList($('#reviewList'), data.dinilai, {withRating:true});
  setupTabs();
  setupSearchAndSort();

  // back button (opsional: ganti URL sesuai app kamu)
  $('#btnBack').addEventListener('click', ()=> history.length>1 ? history.back() : location.href = './profil_pembeli.html');
});
