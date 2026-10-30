// BASE_URL dihitung otomatis dari lokasi halaman (.../public/views/xxx.html -> .../public)
const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];
const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const isAdmin = role === 'admin';

document.querySelector('#info-role').textContent = role;

// Tugas 1: Logout
document.querySelector('#btn-logout').addEventListener('click', () => {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    window.location.href = 'login.html';
});

// Tugas 2: sembunyikan tombol Tambah/Ubah/Hapus bila bukan admin
// (hanya kosmetik; proteksi sebenarnya tetap di backend: Auth::requireAdmin())
if (!isAdmin) {
    document.querySelectorAll('.aksi-admin').forEach(el => el.style.display = 'none');
}

function esc(teks) {
    const d = document.createElement('div');
    d.textContent = teks ?? '';
    return d.innerHTML.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
}

async function panggilApi(path, method = 'GET', body = null) {
    const res = await fetch(`${BASE_URL}${path}`, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
        body: body ? JSON.stringify(body) : undefined,
    });
    const hasil = await res.json().catch(() => ({}));
    if (res.status === 401) {            // token tidak valid / kedaluwarsa
        localStorage.clear();
        window.location.href = 'login.html';
        return null;
    }
    if (res.status === 403) {            // bukan admin
        alert(hasil.message || 'Akses ditolak, hanya admin yang diizinkan');
        return null;
    }
    return hasil;
}

async function muatData() {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    const kolom = isAdmin ? 4 : 3;
    tbody.innerHTML = `<tr><td colspan="${kolom}">Memuat data...</td></tr>`;
    try {
        const hasil = await panggilApi('/mahasiswa');
        if (!hasil) return;
        const data = hasil.data || [];

        // Langkah 5: jumlah total mahasiswa
        document.querySelector('#total-mahasiswa').textContent = data.length;

        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${kolom}">Belum ada data</td></tr>`;
            return;
        }
        tbody.innerHTML = data.map(m => {
            const id = m.id ?? m.nim;
            const aksi = isAdmin
                ? `<td class="aksi-admin">
                     <button onclick="ubahMahasiswa('${esc(id)}', '${esc(m.nim)}', '${esc(m.nama)}', '${esc(m.prodi)}')">Ubah</button>
                     <button onclick="hapusMahasiswa('${esc(id)}')">Hapus</button>
                   </td>`
                : '';
            return `<tr><td>${esc(m.nim)}</td><td>${esc(m.nama)}</td><td>${esc(m.prodi)}</td>${aksi}</tr>`;
        }).join('');
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tbody.innerHTML = `<tr><td colspan="${kolom}">Gagal memuat data</td></tr>`;
    }
}

async function tambahMahasiswa() {
    const nim = prompt('NIM:');
    const nama = nim && prompt('Nama:');
    const prodi = nama && prompt('Program Studi:');
    if (!prodi) return;
    const hasil = await panggilApi('/mahasiswa', 'POST', { nim, nama, prodi });
    if (hasil) muatData();
}

async function ubahMahasiswa(id, nim, nama, prodi) {
    const nimBaru = prompt('NIM:', nim);
    if (nimBaru === null) return;
    const namaBaru = prompt('Nama:', nama);
    if (namaBaru === null) return;
    const prodiBaru = prompt('Program Studi:', prodi);
    if (prodiBaru === null) return;
    const hasil = await panggilApi(`/mahasiswa/${id}`, 'PUT',
        { nim: nimBaru, nama: namaBaru, prodi: prodiBaru });
    if (hasil) muatData();
}

async function hapusMahasiswa(id) {
    if (!confirm('Yakin hapus data ini?')) return;
    const hasil = await panggilApi(`/mahasiswa/${id}`, 'DELETE');
    if (hasil) muatData();
}

document.querySelector('#btn-tambah').addEventListener('click', tambahMahasiswa);
document.addEventListener('DOMContentLoaded', muatData);
