const tbody = document.querySelector('#tabel-mahasiswa tbody');

document.querySelector('#info-role').textContent = role;
document.querySelector('#info-user').textContent = localStorage.getItem('username') || '-';
document.querySelector('#btn-logout').addEventListener('click', () => {
    hapusSesi();
    window.location.replace('login.html');
});
if (!isAdmin) document.querySelectorAll('.aksi-admin').forEach(el => el.style.display = 'none');

async function muatData() {
    const jumlahKolom = isAdmin ? 5 : 4;
    tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Memuat data...</td></tr>`;
    const hasil = await panggilApi('/mahasiswa');
    if (!hasil) { tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Gagal memuat data</td></tr>`; return; }
    const data = Array.isArray(hasil.data) ? hasil.data : [];
    document.querySelector('#total-mahasiswa').textContent = data.length;
    if (!data.length) { tbody.innerHTML = `<tr><td colspan="${jumlahKolom}">Belum ada data mahasiswa</td></tr>`; return; }
    tbody.innerHTML = data.map(m => {
        const gambar = m.foto ? `<img class="foto" src="${esc(fotoUrl(m.foto))}" alt="Foto ${esc(m.nama)}" loading="lazy">` : '<span class="tanpa-foto">Tidak ada</span>';
        const aksi = isAdmin ? `<td class="aksi-admin"><a class="btn" href="form_edit.html?id=${Number(m.id)}">Ubah</a><button type="button" class="hapus" data-id="${Number(m.id)}" data-nama="${esc(m.nama)}">Hapus</button></td>` : '';
        return `<tr><td>${gambar}</td><td>${esc(m.nim)}</td><td>${esc(m.nama)}</td><td>${esc(m.prodi)}</td>${aksi}</tr>`;
    }).join('');
}

async function hapusData(id, nama) {
    if (!confirm(`Hapus data "${nama}" beserta fotonya?`)) return;
    const hasil = await panggilApi(`/mahasiswa/${id}`, { method: 'DELETE' });
    if (hasil) {
        toast(hasil.message || 'Mahasiswa berhasil dihapus');
        await muatData();   // tabel diperbarui tanpa reload halaman
    }
}
tbody.addEventListener('click', (e) => {
    const btn = e.target.closest('button.hapus');
    if (btn) hapusData(btn.dataset.id, btn.dataset.nama);
});

muatData();
