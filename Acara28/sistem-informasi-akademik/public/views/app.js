// BASE_URL dihitung otomatis dari lokasi halaman ini (.../public/views/xxx.html -> .../public)
const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];

async function muatDataMahasiswa() {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');

    // Tugas 1 (Acara 24): indikator memuat
    tbody.innerHTML = '<tr><td colspan="3">Memuat data...</td></tr>';

    try {
        const response = await fetch(`${BASE_URL}/mahasiswa`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const hasil = await response.json();

        // Tugas 2 (Acara 24): data kosong
        if (!hasil.data || hasil.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Belum ada data</td></tr>';
            return;
        }

        tbody.innerHTML = hasil.data.map(mhs =>
            `<tr><td>${mhs.nim}</td><td>${mhs.nama}</td><td>${mhs.prodi}</td></tr>`
        ).join('');
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tbody.innerHTML = '<tr><td colspan="3">Gagal memuat data</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
