const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];
const token = localStorage.getItem('token');
const role = localStorage.getItem('role') || 'user';
const isAdmin = role === 'admin';
const tbody = document.querySelector('#tabel-mahasiswa tbody');

document.querySelector('#info-role').textContent = role;
document.querySelector('#info-user').textContent = localStorage.getItem('username') || '-';

// Logout: hapus seluruh data sesi lokal, kemudian kembali ke halaman login.
document.querySelector('#btn-logout').addEventListener('click', () => {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    localStorage.removeItem('username');
    window.location.replace('login.html');
});

// Penyembunyian tombol hanya untuk tampilan. Keamanan sebenarnya berada di Auth::requireAdmin().
if (!isAdmin) document.querySelectorAll('.aksi-admin').forEach(el => el.style.display = 'none');

function esc(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[char]);
}

async function panggilApi(path, method = 'GET', body = null) {
    const headers = { 'Content-Type': 'application/json' };
    if (token) headers.Authorization = `Bearer ${token}`;
    const response = await fetch(`${BASE_URL}${path}`, {
        method, headers, body: body ? JSON.stringify(body) : undefined
    });
    const hasil = await response.json().catch(() => ({}));
    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        localStorage.removeItem('username');
        alert(hasil.message || 'Sesi berakhir. Silakan login kembali.');
        window.location.replace('login.html');
        return null;
    }
    if (!response.ok || !hasil.success) {
        if (response.status === 403) alert(hasil.message || 'Akses ditolak, hanya admin yang diizinkan');
        else alert(hasil.message || `Permintaan gagal (HTTP ${response.status})`);
        return null;
    }
    return hasil;
}

async function muatData() {
    const jumlahKolom = isAdmin ? 4 : 3;
    tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Memuat data...</td></tr>`;
    try {
        const hasil = await panggilApi('/mahasiswa');
        if (!hasil) return;
        const data = Array.isArray(hasil.data) ? hasil.data : [];
        document.querySelector('#total-mahasiswa').textContent = data.length;
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Belum ada data mahasiswa</td></tr>`;
            return;
        }
        tbody.innerHTML = data.map(m => {
            const aksi = isAdmin ? `<td class="aksi-admin">
                <button type="button" onclick="ubahMahasiswa(${Number(m.id)}, '${esc(m.nim)}', '${esc(m.nama)}', '${esc(m.prodi)}')">Ubah</button>
                <button type="button" class="hapus" onclick="hapusMahasiswa(${Number(m.id)})">Hapus</button>
            </td>` : '';
            return `<tr><td>${esc(m.nim)}</td><td>${esc(m.nama)}</td><td>${esc(m.prodi)}</td>${aksi}</tr>`;
        }).join('');
    } catch (error) {
        console.error(error);
        tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Gagal memuat data mahasiswa</td></tr>`;
    }
}

async function tambahMahasiswa() {
    const nim = prompt('Masukkan NIM:'); if (nim === null) return;
    const nama = prompt('Masukkan nama:'); if (nama === null) return;
    const prodi = prompt('Masukkan program studi:'); if (prodi === null) return;
    if (!nim.trim() || !nama.trim() || !prodi.trim()) return alert('Semua field wajib diisi.');
    const hasil = await panggilApi('/mahasiswa', 'POST', { nim, nama, prodi });
    if (hasil) { alert(hasil.message); await muatData(); }
}

async function ubahMahasiswa(id, nimLama, namaLama, prodiLama) {
    const nim = prompt('NIM:', nimLama); if (nim === null) return;
    const nama = prompt('Nama:', namaLama); if (nama === null) return;
    const prodi = prompt('Program Studi:', prodiLama); if (prodi === null) return;
    if (!nim.trim() || !nama.trim() || !prodi.trim()) return alert('Semua field wajib diisi.');
    const hasil = await panggilApi(`/mahasiswa/${id}`, 'PUT', { nim, nama, prodi });
    if (hasil) { alert(hasil.message); await muatData(); }
}

async function hapusMahasiswa(id) {
    if (!confirm('Yakin ingin menghapus data mahasiswa ini?')) return;
    const hasil = await panggilApi(`/mahasiswa/${id}`, 'DELETE');
    if (hasil) { alert(hasil.message); await muatData(); }
}

if (isAdmin) document.querySelector('#btn-tambah').addEventListener('click', tambahMahasiswa);
document.addEventListener('DOMContentLoaded', muatData);
