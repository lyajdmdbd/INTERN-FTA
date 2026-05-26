// ============================================
// app.js — Sistem Tempahan Projector BAHAGIAN UNTUK FRONTEND
// ============================================

const API = 'api.php';
let currentFilter = 'harian';
let deleteTargetId = null;
let currentView = 'list'; 

function setView(view, btn) {
  currentView = view;
  document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  loadBookings();
}

// ── Utility: Toast Notification ───────────────────────────────────────────
function showToast(message, type = 'info') {
  const icons = {
    success: `<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`,
    error:   `<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`,
    info:    `<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>`
  };
  const el = document.createElement('div');
  el.className = `toast toast-${type}`;
  el.innerHTML = `<span class="toast-icon">${icons[type] || icons.info}</span><span>${message}</span>`;
  document.getElementById('toast-container').appendChild(el);
  setTimeout(() => {
    el.style.transition = 'opacity .3s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 300);
  }, 3500);
}

// ── Modal Popup: Slot Penuh ───────────────────────────────────────────────
function showModalPenuh(message) {
  const lama = document.getElementById('modal-penuh');
  if (lama) lama.remove();

  const modal = document.createElement('div');
  modal.id = 'modal-penuh';
  modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:9999;';
  modal.innerHTML = `
    <div style="background:#fff;border-radius:16px;padding:32px 28px;max-width:360px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
      <div style="width:56px;height:56px;border-radius:50%;background:#FCEBEB;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="28" height="28" fill="none" stroke="#E24B4A" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/>
        </svg>
      </div>
      <h3 style="margin:0 0 8px;font-size:17px;font-weight:600;color:#1a1a1a;">Slot Sudah Penuh!</h3>
      <p style="margin:0 0 24px;font-size:14px;color:#666;line-height:1.6;">${message}</p>
      <button onclick="document.getElementById('modal-penuh').remove()" style="background:#E24B4A;color:#fff;border:none;border-radius:10px;padding:10px 32px;font-size:15px;font-weight:500;cursor:pointer;">
        OK
      </button>
    </div>`;

  modal.addEventListener('click', function(e) {
    if (e.target === this) this.remove();
  });

  document.body.appendChild(modal);
}

// ── Utility: Format tarikh untuk paparan ─────────────────────────────────
const BULAN = ['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sep','Okt','Nov','Dis'];
const BULAN_PENUH = ['Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember'];
const HARI = ['Ahad', 'Isnin', 'Selasa', 'Rabu', 'Khamis', 'Jumaat', 'Sabtu'];

function formatTarikh(d) {
  const dt = new Date(d + 'T00:00:00');
  const namaHari = HARI[dt.getDay()]; 
  return `${namaHari}, ${dt.getDate()} ${BULAN[dt.getMonth()]} ${dt.getFullYear()}`;
} 

function formatTarikhGrup(d, filter) {
  const dt = new Date(d + 'T00:00:00');
  if (filter === 'bulanan') return `${BULAN_PENUH[dt.getMonth()]} ${dt.getFullYear()}`;
  if (filter === 'mingguan') {
    const end = new Date(dt); end.setDate(dt.getDate() + 6);
    return `${dt.getDate()} ${BULAN[dt.getMonth()]} – ${end.getDate()} ${BULAN[end.getMonth()]} ${dt.getFullYear()}`;
  }
  return formatTarikh(d);
}

function kiraDurasi(mula, tamat) {
  const [jamMula, minMula] = mula.split(':').map(Number);
  const [jamTamat, minTamat] = tamat.split(':').map(Number);
  const totalMinitMula = jamMula * 60 + minMula;
  const totalMinitTamat = jamTamat * 60 + minTamat;
  const bezaMinit = totalMinitTamat - totalMinitMula;
  const jam = Math.floor(bezaMinit / 60);
  const minit = bezaMinit % 60;
  let output = "";
  if (jam > 0) output += `${jam} Jam `;
  if (minit > 0) output += `${minit} Minit`;
  return output.trim();
}

function dapatkanTarikhHarini() {
  const harini = new Date();
  const yyyy = harini.getFullYear();
  const mm = String(harini.getMonth() + 1).padStart(2, '0');
  const dd = String(harini.getDate()).padStart(2, '0');
  return `${yyyy}-${mm}-${dd}`;
}

// ── Page Navigation ───────────────────────────────────────────────────────
function showPage(id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function showDashboard() {
  showPage('page-dashboard');
  loadStats();
  loadBookings();
  loadSlotStatus();
}

function showFormPage() {
  resetForm();
  showPage('page-form');
}

// ── Tabs ──────────────────────────────────────────────────────────────────
function setTab(filter, btn) {
  currentFilter = filter;
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelector('.tabs').setAttribute('data-active', filter);
  loadBookings();
}

// ── Load Stats ────────────────────────────────────────────────────────────
async function loadStats() {
  try {
    const res  = await fetch(`${API}?action=stats`);
    const data = await res.json();
    if (data.status === 'ok') {
      document.getElementById('stat-total').textContent   = data.total;
      document.getElementById('stat-hari').textContent    = data.hari_ini;
      document.getElementById('stat-minggu').textContent  = data.minggu;
    }
  } catch (e) { }
}

// ── Load Slot Status ──────────────────────────────────────────────────────
async function loadSlotStatus() {
  const container = document.getElementById('topUserList');
  if (!container) return;

  try {
    const res  = await fetch(`${API}?action=slot_status`);
    const data = await res.json();
    if (data.status !== 'ok') return;

    let html = '';
    data.slots.forEach(slot => {
      const penuh = slot.count >= slot.max;
      const pct   = Math.min(Math.round((slot.count / slot.max) * 100), 100);
      const baki  = slot.max - slot.count;

      const warnaBadge = penuh
        ? 'background:#FCEBEB;color:#A32D2D;'
        : 'background:#EAF3DE;color:#3B6D11;';
      const warnaBar = penuh ? '#E24B4A' : '#639922';
      const labelStatus = penuh ? 'PENUH' : `${baki} slot lagi`;

      html += `
        <div style="margin-bottom:14px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
            <span style="font-size:14px;font-weight:600;color:#fff;">${slot.label}</span>
            <span style="font-size:14px;font-weight:600;padding:2px 8px;border-radius:20px;${warnaBadge}">${labelStatus}</span>
          </div>
          <div style="background:rgba(255,255,255,0.15);border-radius:99px;height:6px;overflow:hidden;">
            <div style="width:${pct}%;height:100%;background:${warnaBar};border-radius:99px;transition:width 0.5s;"></div>
          </div>
          <div style="font-size:13px;color:rgba(255,255,255,0.55);margin-top:4px;">${slot.count} / ${slot.max} tempahan</div>
        </div>`;
    });

    container.innerHTML = html;
  } catch (e) {
    container.innerHTML = `<div style="font-size:12px;color:rgba(255,255,255,0.4);text-align:center;padding:10px 0;">Ralat memuatkan</div>`;
  }
}

function getDeviceIcon(device) {
   if (device === 'tablet') {
    return `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <rect x="5" y="2" width="14" height="20" rx="2"/>
              <circle cx="12" cy="18" r="1" fill="currentColor" stroke="none"/>
            </svg>`;
  }
  if (device === 'ipad') {
    return `<svg viewBox="0 0 24 24" fill="currentColor" stroke="none">
              <path d="M17.05 12.536c-.02-2.137 1.753-3.17 1.833-3.22-1.002-1.463-2.557-1.663-3.105-1.682-1.317-.134-2.578.775-3.247.775-.67 0-1.694-.757-2.789-.736-1.427.021-2.75.83-3.484 2.1-1.49 2.582-.381 6.4 1.065 8.494.709 1.02 1.551 2.163 2.652 2.122 1.067-.042 1.468-.684 2.757-.684 1.289 0 1.651.684 2.774.662 1.148-.02 1.874-1.04 2.574-2.066.818-1.18 1.152-2.333 1.17-2.393-.026-.011-2.173-.832-2.2-3.372z" fill="currentColor"/>
              <path d="M14.69 5.61c.576-.706.967-1.68.859-2.658-.833.036-1.867.564-2.468 1.254-.531.613-.999 1.612-.876 2.562.934.07 1.886-.47 2.485-1.158z" fill="currentColor"/>
            </svg>`;
  }
  return `<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <rect x="2" y="6" width="20" height="10" rx="2"/>
  <circle cx="17" cy="11" r="2"/>
  <path stroke-linecap="round" d="M5 11h6"/>
  <path stroke-linecap="round" d="M10 16l-2 3M14 16l2 3"/>
</svg>`;
}

// ── Load & Render Bookings ────────────────────────────────────────────────
async function loadBookings() {
  const container = document.getElementById('list-container');
  container.innerHTML = `<div class="loading"><div class="spinner"></div>Memuatkan data...</div>`;

  try {
    const res  = await fetch(`${API}?action=get_all&filter=${currentFilter}`);
    const data = await res.json();

    if (data.status !== 'ok' || data.data.length === 0) {
      container.innerHTML = `
        <div class="empty-state">
          <div class="empty-icon">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
              <rect x="3" y="4" width="18" height="16" rx="2"/>
              <path stroke-linecap="round" d="M8 2v3m8-3v3M3 10h18"/>
            </svg>
          </div>
          <p>Tiada rekod tempahan ditemui.</p>
          <span>Klik "Tambah Tempahan" untuk mula menempah.</span>
        </div>`;
      return;
    }

    const groups = {};
    data.data.forEach(b => {
      const key = currentFilter === 'bulanan' ? b.tarikh.slice(0, 7) : b.tarikh;
      if (!groups[key]) groups[key] = [];
      groups[key].push(b);
    });

    const keys = Object.keys(groups).sort((a, b) => b.localeCompare(a));
    let html = '';

    keys.forEach(key => {
      const labelDate = currentFilter === 'bulanan' ? key + '-01' : key;
      html += `<div class="group-label">${formatTarikhGrup(labelDate, currentFilter)}</div>`;

      if (currentView === 'list') {
        groups[key].forEach(b => {
          const device = b.device || 'projector';

          const adaMasa = b.masa_mula && b.masa_tamat
            && b.masa_mula !== '00:00:00' && b.masa_tamat !== '00:00:00'
            && b.masa_mula !== '00:00' && b.masa_tamat !== '00:00';

          const durasiTeks = adaMasa ? kiraDurasi(b.masa_mula, b.masa_tamat) : '';

          const durasiHtml = durasiTeks
            ? `<span style="opacity:0.3;margin:0 8px;">|</span><span style="color:#4f46e5;font-weight:600;">${durasiTeks}</span>`
            : '';

          const masaHtml = adaMasa
            ? `<span class="meta-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
                ${b.masa_mula.slice(0,5)} – ${b.masa_tamat.slice(0,5)}
              </span>`
            : '';

          const kelasHtml = b.kelas
            ? `${escHtml(b.kelas)}${durasiHtml}`
            : durasiHtml;

          const noLimitHtml = (device === 'tablet' )
            ? `<span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;margin-left:4px;
                            background:${device==='tablet'?'#EEEDFE':'#E1F5EE'};
                            color:${device==='tablet'?'#534AB7':'#0F6E56'};">∞ Max 2</span>`
            : '';

          html += `
            <div class="booking-card device-${device}" id="card-${b.id}">
              <div class="card-icon-wrap">
                ${getDeviceIcon(device)}
              </div>
              <div class="card-body">
                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:4px;margin-bottom:4px;">
                  <div class="card-badge">${kelasHtml}</div>
                  ${noLimitHtml}
                </div>
                <div class="card-name">${escHtml(b.nama)}</div>
                <div class="card-meta">
                  <span class="meta-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>
                    ${formatTarikh(b.tarikh)}
                  </span>
                  ${masaHtml}
                </div>
                <div class="card-tujuan">${escHtml(b.tujuan)}</div>
              </div>
              <div class="card-actions">
                <button class="btn btn-danger" onclick="confirmDelete(${b.id}, '${escHtml(b.nama)}')">
                  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                  </svg>
                  Padam
                </button>
              </div>
            </div>`;
        });

      } else {
        html += `<div class="grid-3">`;
        const warna3 = ['#378ADD', '#EF9F27', '#8c8c8c'];
        let warnaIndex = 0;
        groups[key].forEach(b => {
          const device = b.device || 'projector';
          const adaMasa = b.masa_mula && b.masa_tamat
            && b.masa_mula !== '00:00:00' && b.masa_tamat !== '00:00:00'
            && b.masa_mula !== '00:00' && b.masa_tamat !== '00:00';
          const durasiTeks = adaMasa ? kiraDurasi(b.masa_mula, b.masa_tamat) : '';

          const warnaGrid = device === 'tablet' ? '#F57C00'
                          : device === 'ipad'   ? '#1D9E75'
                          : warna3[warnaIndex % warna3.length];
          if (device === 'projector') warnaIndex++;

          const masaGridHtml = adaMasa
            ? `<span class="meta-item">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
                ${b.masa_mula.slice(0,5)} – ${b.masa_tamat.slice(0,5)}
              </span>`
            : '';

          const badgeGridHtml = (device === 'tablet')
            ? `<span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:20px;
                            background:${device==='tablet'?'#EEEDFE':'#E1F5EE'};
                            color:${device==='tablet'?'#534AB7':'#0F6E56'};">∞ Max 2</span>`
            : `<span class="durasi-badge">${durasiTeks}</span>`;

          const kelasGridHtml = b.kelas
            ? `<div class="card-badge">${escHtml(b.kelas)}</div>`
            : '';

          html += `
            <div class="booking-card device-${device}" id="card-${b.id}" style="border-left: 3px solid ${warnaGrid}; border-radius: 0 12px 12px 0;">
              <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;">
                ${kelasGridHtml}
                ${badgeGridHtml}
              </div>
              <div class="card-name">${escHtml(b.nama)}</div>
              <div class="card-meta">
                <span class="meta-item">
                  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                  </svg>
                  ${formatTarikh(b.tarikh)}
                </span>
                ${masaGridHtml}
              </div>
              <div class="card-tujuan">${escHtml(b.tujuan)}</div>
              <div class="card-actions">
                <button class="btn btn-danger" onclick="confirmDelete(${b.id}, '${escHtml(b.nama)}')">
                  <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                  </svg>
                  Padam
                </button>
              </div>
            </div>`;
        });
        html += `</div>`;
      }
    });

    container.innerHTML = html;
  } catch (e) {
    container.innerHTML = `<div class="empty-state"><p>Ralat memuatkan data. Pastikan server WAMP aktif.</p></div>`;
  }
}

function escHtml(str) {
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// ── DELETE dengan Modal ───────────────────────────────────────────────────
function confirmDelete(id, nama) {
  console.log("ID:", id, "Nama:", nama);
  deleteTargetId = id;
  document.getElementById('delete-nama').textContent = nama;
  document.getElementById('modal-confirm').classList.add('open');
}

function closeModal(reset = true) {
  document.getElementById('modal-confirm').classList.remove('open');
  if (reset) deleteTargetId = null;
}

async function doDelete() {
  if (!deleteTargetId) {
    showToast('Tiada data dipilih untuk dipadam.', 'error');
    return;
  }

  const id = deleteTargetId;
  closeModal();

  try {
    const res = await fetch(`${API}?action=delete`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    });

    const data = await res.json();

    if (data.status === 'ok') {
      showToast(data.message || 'Data berjaya dipadam!', 'success');
      const card = document.getElementById(`card-${id}`);
      if (card) card.style.transition = 'opacity 0.3s';
      if (card) card.style.opacity = '0';
      setTimeout(() => {
        if (card) card.remove();
        if (!document.querySelector('.booking-card')) loadBookings();
      }, 300);
      loadStats();
      loadSlotStatus();
    } else {
      showToast(data.message || 'Ralat memadam data.', 'error');
    }

  } catch (e) {
    console.error('Delete error:', e);
    showToast('Gagal menghubungi server. Pastikan WAMP aktif.', 'error');
  }
}

// ── Form Validation & Submit ──────────────────────────────────────────────
function resetForm() {
  document.getElementById('booking-form').reset();
  document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
  const tarikhInput = document.getElementById('f-tarikh');
  if (tarikhInput) tarikhInput.setAttribute('min', dapatkanTarikhHarini());

  //  reset device selector kepada projector & papar balik kelas-row
  document.querySelectorAll('.device-option').forEach(el => el.classList.remove('active'));
  const defDev = document.querySelector('.device-option[data-device="projector"]');
  if (defDev) defDev.classList.add('active');
  const kelasRow = document.getElementById('kelas-row');
  if (kelasRow) kelasRow.style.display = '';
}

function setInvalid(id, msg) {
  const el = document.getElementById(id);
  el.classList.add('is-invalid');
  const fb = el.nextElementSibling;
  if (fb && fb.classList.contains('invalid-feedback')) fb.textContent = msg;
}

function clearInvalid(id) {
  const el = document.getElementById(id);
  el.classList.remove('is-invalid');
}

async function submitForm() {
  console.log('submitForm dipanggil!'); 
  let valid = true;

  // baca device yang dipilih
  const device = document.querySelector('input[name="f-device"]:checked')?.value || 'projector';

  //  kelas hanya wajib untuk projector
  const fields = ['f-nama', 'f-tarikh', 'f-mula', 'f-tamat', 'f-tujuan'];
  if (device === 'projector') fields.push('f-kelas');
  fields.forEach(id => clearInvalid(id));

  const nama   = document.getElementById('f-nama').value.trim();
  const tarikh = document.getElementById('f-tarikh').value;
  const mula   = document.getElementById('f-mula').value;
  const tamat  = document.getElementById('f-tamat').value;
  const kelas  = document.getElementById('f-kelas').value;
  const tujuan = document.getElementById('f-tujuan').value.trim();

  if (!nama)   { setInvalid('f-nama',   'Sila masukkan nama.'); valid = false; }
  if (!tarikh) { setInvalid('f-tarikh', 'Sila pilih tarikh.'); valid = false; }
  if (!tujuan) { setInvalid('f-tujuan', 'Sila isi tujuan.'); valid = false; }

  // kelas wajib hanya untuk projector
  if (device === 'projector' && !kelas) {
    setInvalid('f-kelas', 'Sila pilih kelas.'); valid = false;
  }

  // Masa wajib untuk SEMUA device
  if (!mula || !tamat) {
    setInvalid('f-mula', 'Sila isi masa mula dan masa tamat.');
    valid = false;
  } else if (tamat <= mula) {
    setInvalid('f-mula', 'Masa tamat mesti lebih daripada masa mula.');
    valid = false;
  } else {
    // Semak masa lepas hanya untuk hari ini
    const sekarang = new Date();
    const jamSekarang = sekarang.getHours().toString().padStart(2, '0');
    const minitSekarang = sekarang.getMinutes().toString().padStart(2, '0');
    const waktuSekarang = `${jamSekarang}:${minitSekarang}`;
    const harini = dapatkanTarikhHarini();

    if (tarikh === harini && mula < waktuSekarang) {
      showToast(`Masa ${mula} sudah berlalu. Jam sekarang ialah ${waktuSekarang}.`, 'error');
      setInvalid('f-mula', 'Waktu ini sudah lepas.');
      valid = false;
    }
  }

  if (!valid) return;

  const btn = document.querySelector('.btn-submit');
  btn.disabled = true;
  btn.innerHTML = `<div class="spinner" style="width:16px;height:16px;border-width:2px;"></div> Menghantar...`;

  try {
    const res  = await fetch(`${API}?action=tambah`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nama,
        tarikh,
        masa_mula: mula,   // semua device hantar masa
        masa_tamat: tamat, // semua device hantar masa
        kelas: device === 'projector' ? kelas : '', // tablet/ipad hantar kelas kosong
        tujuan,
        device
      })
    });
    const data = await res.json();

    if (data.status === 'ok') {
      showToast(data.message, 'success');
      showDashboard();
    } else if (data.status === 'penuh') {
      showModalPenuh(data.message);
      showDashboard();
    } else {
      showToast(data.message || 'Ralat berlaku.', 'error');
    }
  } catch (e) {
    showToast('Gagal menghubungi server. Pastikan WAMP / Xampp aktif.', 'error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = `<svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Hantar Tempahan`;
  }
}

// ── Event Listeners ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modal-confirm');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === this) closeModal();
    });
  }
  const tarikhInput = document.getElementById('f-tarikh');
  if (tarikhInput) tarikhInput.setAttribute('min', dapatkanTarikhHarini());
});

// ── 3D Card Tilt Effect ───────────────────────────────────────────────────
document.addEventListener('mousemove', (e) => {
  const cards = document.querySelectorAll('.booking-card');
  cards.forEach(card => {
    const rect = card.getBoundingClientRect();
    const isHover =
      e.clientX >= rect.left && e.clientX <= rect.right &&
      e.clientY >= rect.top  && e.clientY <= rect.bottom;

    if (isHover) {
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      const cx = rect.width / 2;
      const cy = rect.height / 2;
      const rotateX = ((y - cy) / cy) * -4;
      const rotateY = ((x - cx) / cx) *  4;
      card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px) scale(1.01)`;
      card.style.boxShadow = `${-rotateY * 2}px ${rotateX * 2 + 16}px 32px rgba(0,0,0,0.15), 0 8px 16px rgba(49,113,241,0.12)`;
    } else {
      card.style.transition = 'transform 0.5s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.5s cubic-bezier(0.23, 1, 0.32, 1)';
      card.style.transform = '';
      card.style.boxShadow = '';
    }
  });
});

// ── Init ──────────────────────────────────────────────────────────────────
showDashboard();

// ── Clock & Calendar ──────────────────────────────────────────────────────
const HARI_PANEL  = ['Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu'];
const BULAN_PANEL = ['Januari','Februari','Mac','April','Mei','Jun','Julai','Ogos','September','Oktober','November','Disember'];
const BULAN_SHORT_PANEL = ['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sep','Okt','Nov','Dis'];

function buildCalPanel() {
  const now = new Date();
  const y = now.getFullYear(), m = now.getMonth();
  const firstDay = new Date(y, m, 1).getDay();
  const daysInMonth = new Date(y, m+1, 0).getDate();
  const container = document.getElementById('ccCalDays');
  if (!container) return;

  let html = '';
  for (let i = 0; i < firstDay; i++) html += `<div class="cc-cal-num"></div>`;
  for (let d = 1; d <= daysInMonth; d++) {
    const isToday = d === now.getDate();
    html += `<div class="cc-cal-num${isToday ? ' today' : ''}">${d}</div>`;
  }
  container.innerHTML = html;

  const monthEl = document.getElementById('ccCalMonth');
  if (monthEl) monthEl.textContent = `${BULAN_PANEL[m]} ${y}`;
}

function tickClock() {
  const now = new Date();
  const h  = String(now.getHours()).padStart(2,'0');
  const mi = String(now.getMinutes()).padStart(2,'0');
  const s  = String(now.getSeconds()).padStart(2,'0');

  const timeEl = document.getElementById('ccTime');
  const dateEl = document.getElementById('ccDate');
  const secsEl = document.getElementById('ccSecsBar');

  if (timeEl) timeEl.textContent = `${h}:${mi}:${s}`;
  if (dateEl) dateEl.textContent = `${HARI_PANEL[now.getDay()]}, ${now.getDate()} ${BULAN_SHORT_PANEL[now.getMonth()]} ${now.getFullYear()}`;
  if (secsEl) secsEl.style.width = (now.getSeconds() / 60 * 100) + '%';
}

buildCalPanel();
tickClock();
setInterval(tickClock, 1000);

// ── Navbar hide on scroll ─────────────────────────────────────────────────
let kedudukanSkrolTerakhir = window.scrollY;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
  const kedudukanSkrolSekarang = window.scrollY;
  if (kedudukanSkrolSekarang > kedudukanSkrolTerakhir && kedudukanSkrolSekarang > 100) {
    navbar.classList.add('navbar-hidden');
  } else {
    navbar.classList.remove('navbar-hidden');
  }
  kedudukanSkrolTerakhir = kedudukanSkrolSekarang;
});

// ── Tunggu DOM sedia ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

  // Muat stat sidebar selepas app.js siap init (showDashboard dipanggil dari app.js)
  // Guna sedikit delay supaya API call dari app.js sempat selesai dulu
  setTimeout(sbLoadStats, 800);

  // Pantau perubahan pada list-container (apabila app.js render semula kad)
  // Guna MutationObserver supaya tidak perlu ubah app.js langsung
  const listContainer = document.getElementById('list-container');
  if (listContainer) {
    const observer = new MutationObserver(function () {
      sbApplyFilter();
      sbUpdateCounts();
    });
    observer.observe(listContainer, { childList: true, subtree: true });
  }
});

// ── Filter aktif semasa ───────────────────────────────────────────────────
let sbActiveDevice = 'semua';

// ── Set filter apabila pill diklik ───────────────────────────────────────
function sbSetFilter(el) {
  // Buang active semua pill
  document.querySelectorAll('.sb-pill').forEach(function (p) {
    p.classList.remove('active');
  });
  el.classList.add('active');
  sbActiveDevice = el.getAttribute('data-device');
  sbApplyFilter();
}

// ── Tapis kad mengikut device ─────────────────────────────────────────────
function sbApplyFilter() {
  var cards = document.querySelectorAll('#list-container .booking-card');
  cards.forEach(function (card) {
    if (sbActiveDevice === 'semua') {
      card.classList.remove('sb-hidden');
    } else {
      // Baca class device pada kad: device-projector / device-tablet / device-ipad
      if (card.classList.contains('device-' + sbActiveDevice)) {
        card.classList.remove('sb-hidden');
      } else {
        card.classList.add('sb-hidden');
      }
    }
  });

  // Sembunyikan group-label jika semua kad dalam kumpulan tersembunyi
  var groupLabels = document.querySelectorAll('#list-container .group-label');
  groupLabels.forEach(function (label) {
    var next = label.nextElementSibling;
    var adaYangKelihatan = false;
    while (next && !next.classList.contains('group-label')) {
      if (next.classList.contains('booking-card') && !next.classList.contains('sb-hidden')) {
        adaYangKelihatan = true;
      }
      // Grid wrapper
      if (next.classList.contains('grid-3') || next.classList.contains('grid-2')) {
        var cardsInGrid = next.querySelectorAll('.booking-card:not(.sb-hidden)');
        if (cardsInGrid.length > 0) adaYangKelihatan = true;
      }
      next = next.nextElementSibling;
    }
    label.style.display = adaYangKelihatan ? '' : 'none';
  });
}

// ── Kira bilangan kad setiap device ──────────────────────────────────────
function sbUpdateCounts() {
  var all      = document.querySelectorAll('#list-container .booking-card').length;
  var projector = document.querySelectorAll('#list-container .booking-card.device-projector').length;
  var tablet   = document.querySelectorAll('#list-container .booking-card.device-tablet').length;
  var ipad     = document.querySelectorAll('#list-container .booking-card.device-ipad').length;

  sbSetCount('sb-cnt-semua',      all);
  sbSetCount('sb-cnt-projector',  projector);
  sbSetCount('sb-cnt-tablet',     tablet);
  sbSetCount('sb-cnt-ipad',       ipad);
}

function sbSetCount(id, val) {
  var el = document.getElementById(id);
  if (el) el.textContent = val;
}

// ── Muat statistik hari ini dari API ─────────────────────────────────────
// Guna endpoint yang sama dengan app.js: api.php?action=stats_device
// Jika endpoint baharu belum ada, fallback ke kira dari DOM
async function sbLoadStats() {
  try {
    var res  = await fetch('api.php?action=stats_device');
    var data = await res.json();

    if (data.status === 'ok') {
      sbSetStat('projector', data.projector, data.total);
      sbSetStat('tablet',    data.tablet,    data.total);
      sbSetStat('ipad',      data.ipad,      data.total);
    } else {
      // Fallback: kira dari DOM jika endpoint belum ada
      sbStatFromDom();
    }
  } catch (e) {
    // Fallback jika endpoint belum ada di api.php
    sbStatFromDom();
  }
}

// ── Stat dari DOM (fallback) ──────────────────────────────────────────────
function sbStatFromDom() {
  var p = document.querySelectorAll('#list-container .booking-card.device-projector').length;
  var t = document.querySelectorAll('#list-container .booking-card.device-tablet').length;
  var i = document.querySelectorAll('#list-container .booking-card.device-ipad').length;
  var total = p + t + i || 1;

  sbSetStat('projector', p, total);
  sbSetStat('tablet',    t, total);
  sbSetStat('ipad',      i, total);
}

function sbSetStat(device, count, total) {
  var numEl  = document.getElementById('sb-stat-' + device);
  var fillEl = document.getElementById('sb-fill-' + device);
  if (numEl)  numEl.textContent  = count;
  if (fillEl) fillEl.style.width = (total > 0 ? Math.round((count / total) * 100) : 0) + '%';
}

// ── Reload stat setiap kali showDashboard dipanggil ───────────────────────
// Patch ringan: wrap showDashboard asal tanpa ubah app.js
(function () {
  // Tunggu app.js load dulu
  var maxTry = 20;
  var tries  = 0;
  var timer  = setInterval(function () {
    tries++;
    if (typeof showDashboard === 'function') {
      clearInterval(timer);
      var _orig = showDashboard;
      // Override showDashboard dengan versi baru yang panggil asal dulu
      window.showDashboard = function () {
        _orig();
        // Tunggu  bagi app.js render list dulu, baru update sidebar
        setTimeout(function () {
          sbLoadStats();
          sbUpdateCounts();
          sbApplyFilter();
        }, 600);
      };
    }
    if (tries >= maxTry) clearInterval(timer);
  }, 100);
})();

