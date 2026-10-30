// ===== Utilitas bersama Acara 27: notifikasi toast, validasi View, pemanggil API =====
const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];
const token = localStorage.getItem('token');
const role = localStorage.getItem('role') || 'user';
const isAdmin = role === 'admin';

function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function fotoUrl(nama) { return nama ? `${BASE_URL}/uploads/${encodeURIComponent(nama)}` : ''; }

// ---------- Notifikasi visual (Tugas 1) ----------
function toast(pesan, tipe = 'sukses') {
    let wadah = document.getElementById('toast-wadah');
    if (!wadah) {
        wadah = document.createElement('div');
        wadah.id = 'toast-wadah';
        wadah.setAttribute('aria-live', 'polite');
        document.body.appendChild(wadah);
    }
    const el = document.createElement('div');
    el.className = `toast toast-${tipe}`;
    el.setAttribute('role', tipe === 'gagal' ? 'alert' : 'status');
    el.innerHTML = `<span class="toast-ikon">${tipe === 'sukses' ? '✓' : '✕'}</span><span class="toast-teks">${esc(pesan)}</span><button type="button" class="toast-tutup" aria-label="Tutup">×</button>`;
    const tutup = () => { el.classList.add('hilang'); setTimeout(() => el.remove(), 250); };
    el.querySelector('.toast-tutup').addEventListener('click', tutup);
    wadah.appendChild(el);
    setTimeout(tutup, tipe === 'gagal' ? 6000 : 3500);
}
// Pesan yang harus tetap tampil setelah berpindah halaman (mis. simpan -> kembali ke dashboard)
function toastSetelahPindah(pesan, tipe = 'sukses') {
    sessionStorage.setItem('flash', JSON.stringify({ pesan, tipe }));
}
(function tampilkanFlash() {
    const raw = sessionStorage.getItem('flash');
    if (!raw) return;
    sessionStorage.removeItem('flash');
    try { const f = JSON.parse(raw); document.addEventListener('DOMContentLoaded', () => toast(f.pesan, f.tipe)); } catch (e) {}
})();

// ---------- Validasi sisi View (Tugas 2) ----------
// aturan: { idField: 'Label' } -> semua wajib diisi (setelah trim)
function validasiWajibIsi(form, aturan) {
    let pertama = null;
    for (const [id, label] of Object.entries(aturan)) {
        const input = form.querySelector('#' + id);
        const kosong = input.value.trim() === '';
        setErrorField(input, kosong ? `${label} wajib diisi.` : '');
        if (kosong && !pertama) pertama = input;
    }
    if (pertama) pertama.focus();
    return !pertama;
}
function validasiFoto(input) {
    const file = input.files[0];
    let pesan = '';
    if (file && !['image/jpeg', 'image/png'].includes(file.type)) pesan = 'Foto harus berformat JPG atau PNG.';
    else if (file && file.size > 2 * 1024 * 1024) pesan = 'Ukuran foto maksimal 2MB.';
    setErrorField(input, pesan);
    if (pesan) input.focus();
    return !pesan;
}
function setErrorField(input, pesan) {
    let el = input.parentElement.querySelector(`.error-field[data-untuk="${input.id}"]`);
    if (!el) {
        el = document.createElement('div');
        el.className = 'error-field';
        el.dataset.untuk = input.id;
        input.insertAdjacentElement('afterend', el);
    }
    el.textContent = pesan;
    input.classList.toggle('invalid', !!pesan);
    input.setAttribute('aria-invalid', pesan ? 'true' : 'false');
}
function bersihkanErrorSaatMengetik(form) {
    form.querySelectorAll('input').forEach(i => i.addEventListener('input', () => setErrorField(i, '')));
}

// ---------- Pemanggil API: selalu mengirim header Authorization ----------
function hapusSesi() { ['token', 'role', 'username'].forEach(k => localStorage.removeItem(k)); }
async function panggilApi(path, options = {}) {
    const headers = { ...(options.headers || {}), Authorization: `Bearer ${token}` };
    let response;
    try {
        response = await fetch(`${BASE_URL}${path}`, { ...options, headers });
    } catch (e) {
        toast('Tidak dapat menghubungi server. Periksa koneksi Anda.', 'gagal');
        return null;
    }
    const hasil = await response.json().catch(() => ({}));
    if (response.status === 401) {
        hapusSesi();
        toastSetelahPindah(hasil.message || 'Sesi berakhir. Silakan login kembali.', 'gagal');
        window.location.replace('login.html');
        return null;
    }
    if (!response.ok || !hasil.success) {
        toast(hasil.message || `Permintaan gagal (HTTP ${response.status})`, 'gagal');
        return null;
    }
    return hasil;
}
