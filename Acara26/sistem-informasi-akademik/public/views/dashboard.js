const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];
const token = localStorage.getItem('token');
const role = localStorage.getItem('role') || 'user';
const isAdmin = role === 'admin';
const tbody = document.querySelector('#tabel-mahasiswa tbody');
const dialog = document.querySelector('#dialog-mahasiswa');
const form = document.querySelector('#form-mahasiswa');

 document.querySelector('#info-role').textContent = role;
document.querySelector('#info-user').textContent = localStorage.getItem('username') || '-';
document.querySelector('#btn-logout').addEventListener('click', () => {
    localStorage.removeItem('token'); localStorage.removeItem('role'); localStorage.removeItem('username');
    window.location.replace('login.html');
});
if (!isAdmin) document.querySelectorAll('.aksi-admin').forEach(el => el.style.display = 'none');

function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function fotoUrl(nama) { return nama ? `${BASE_URL}/uploads/${encodeURIComponent(nama)}` : ''; }
async function panggilApi(path, options = {}) {
    const headers = { ...(options.headers || {}), Authorization: `Bearer ${token}` };
    const response = await fetch(`${BASE_URL}${path}`, { ...options, headers });
    const hasil = await response.json().catch(() => ({}));
    if (response.status === 401) {
        localStorage.removeItem('token'); localStorage.removeItem('role'); localStorage.removeItem('username');
        alert(hasil.message || 'Sesi berakhir. Silakan login kembali.'); window.location.replace('login.html'); return null;
    }
    if (!response.ok || !hasil.success) {
        alert(hasil.message || `Permintaan gagal (HTTP ${response.status})`); return null;
    }
    return hasil;
}
async function muatData() {
    const jumlahKolom = isAdmin ? 5 : 4;
    tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Memuat data...</td></tr>`;
    const hasil = await panggilApi('/mahasiswa');
    if (!hasil) return;
    const data = Array.isArray(hasil.data) ? hasil.data : [];
    document.querySelector('#total-mahasiswa').textContent = data.length;
    if (!data.length) { tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Belum ada data mahasiswa</td></tr>`; return; }
    tbody.innerHTML = data.map(m => {
        const gambar = m.foto ? `<img class="foto" src="${esc(fotoUrl(m.foto))}" alt="Foto ${esc(m.nama)}" loading="lazy">` : '<span class="tanpa-foto">Tidak ada</span>';
        const aksi = isAdmin ? `<td class="aksi-admin"><button type="button" onclick="ubahMahasiswa(${Number(m.id)})">Ubah</button><button type="button" class="hapus" onclick="hapusMahasiswa(${Number(m.id)})">Hapus</button></td>` : '';
        return `<tr><td>${gambar}</td><td>${esc(m.nim)}</td><td>${esc(m.nama)}</td><td>${esc(m.prodi)}</td>${aksi}</tr>`;
    }).join('');
}
function bukaForm(data = null) {
    form.reset();
    document.querySelector('#mahasiswa-id').value = data?.id ?? '';
    document.querySelector('#judul-form').textContent = data ? 'Ubah Mahasiswa' : 'Tambah Mahasiswa';
    document.querySelector('#nim').value = data?.nim ?? '';
    document.querySelector('#nama').value = data?.nama ?? '';
    document.querySelector('#prodi').value = data?.prodi ?? '';
    document.querySelector('#info-foto').textContent = data?.foto ? 'Foto saat ini tersedia. Pilih foto baru jika ingin menggantinya; kosongkan untuk mempertahankan foto lama.' : 'Foto boleh dikosongkan.';
    dialog.showModal();
}
document.querySelector('#btn-tambah').addEventListener('click', () => bukaForm());
document.querySelector('#btn-batal').addEventListener('click', () => dialog.close());
window.ubahMahasiswa = async (id) => {
    const hasil = await panggilApi('/mahasiswa');
    const data = hasil?.data?.find(m => Number(m.id) === Number(id));
    if (data) bukaForm(data);
};
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const id = document.querySelector('#mahasiswa-id').value;
    const file = document.querySelector('#foto').files[0];
    if (file && file.size > 2 * 1024 * 1024) return alert('Ukuran foto maksimal 2MB.');
    if (file && !['image/jpeg', 'image/png'].includes(file.type)) return alert('Foto harus berformat JPG atau PNG.');
    const data = new FormData(form);
    if (id) data.append('_method', 'PUT');
    const button = document.querySelector('#btn-simpan'); button.disabled = true;
    try {
        const hasil = await panggilApi(id ? `/mahasiswa/${id}` : '/mahasiswa', { method: 'POST', body: data });
        if (hasil) { dialog.close(); alert(hasil.message); await muatData(); }
    } finally { button.disabled = false; }
});
window.hapusMahasiswa = async (id) => {
    if (!confirm('Yakin ingin menghapus data mahasiswa ini beserta fotonya?')) return;
    const hasil = await panggilApi(`/mahasiswa/${id}`, { method: 'DELETE' });
    if (hasil) { alert(hasil.message); await muatData(); }
};
muatData();
