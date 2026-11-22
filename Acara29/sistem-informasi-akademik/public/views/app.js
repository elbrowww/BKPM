// BASE_URL dihitung otomatis dari lokasi halaman ini (.../public/views/xxx.html -> .../public)
const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];

// Acara 29: escaping output agar data dinamis tampil sebagai teks, bukan HTML
function escapeHTML(teks) {
    return String(teks ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

// Acara 29: retry otomatis satu kali bila fetch gagal karena jaringan (hanya GET, aman diulang)
async function fetchDenganRetry(url, options = {}, jeda = 800) {
    try {
        return await fetch(url, options);
    } catch (e) {
        await new Promise(r => setTimeout(r, jeda));
        return await fetch(url, options);
    }
}

function renderTabel(data) {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3">Belum ada data</td></tr>';
        return;
    }
    tbody.innerHTML = data.map(mhs =>
        `<tr><td>${escapeHTML(mhs.nim)}</td><td>${escapeHTML(mhs.nama)}</td><td>${escapeHTML(mhs.prodi)}</td></tr>`
    ).join('');
}

async function muatDataMahasiswa() {
    const tbody = document.querySelector('#tabel-mahasiswa tbody');
    tbody.innerHTML = '<tr><td colspan="3">Memuat data...</td></tr>';
    try {
        const response = await fetchDenganRetry(`${BASE_URL}/mahasiswa`);
        if (!response.ok) throw new Error('Server merespons dengan status ' + response.status);
        const hasil = await response.json();
        renderTabel(hasil.data);
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tbody.innerHTML = `<tr><td colspan="3">Gagal memuat data: ${escapeHTML(error.message)}</td></tr>`;
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
