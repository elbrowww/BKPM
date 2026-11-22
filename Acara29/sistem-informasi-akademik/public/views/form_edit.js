const form = document.querySelector('#form-edit');
const id = new URLSearchParams(window.location.search).get('id');
const nim = document.querySelector('#nim'), nama = document.querySelector('#nama'), prodi = document.querySelector('#prodi');
bersihkanErrorSaatMengetik(form);

async function muatDataUntukEdit(id) {
    const hasil = await panggilApi(`/mahasiswa?id=${encodeURIComponent(id)}`);
    if (!hasil) { setTimeout(() => window.location.replace('dashboard.html'), 1800); return; }
    nim.value = hasil.data.nim; nama.value = hasil.data.nama; prodi.value = hasil.data.prodi;
    if (hasil.data.foto) {
        document.querySelector('#foto-saat-ini').innerHTML =
            `<img class="foto-lama" src="${esc(fotoUrl(hasil.data.foto))}" alt="Foto saat ini"><p class="catatan">Foto saat ini</p>`;
    }
}

async function simpanPerubahan(id) {
    // Validasi sisi View sebelum dikirim ke Controller
    const okTeks = validasiWajibIsi(form, { nim: 'NIM', nama: 'Nama', prodi: 'Program Studi' });
    const okFoto = validasiFoto(form.querySelector('#foto'));
    if (!okTeks || !okFoto) { toast('Periksa kembali isian formulir.', 'gagal'); return; }

    const data = new FormData();
    data.append('nim', nim.value.trim());
    data.append('nama', nama.value.trim());
    data.append('prodi', prodi.value.trim());
    const file = form.querySelector('#foto').files[0];
    if (file) data.append('foto', file);
    data.append('_method', 'PUT');   // PHP hanya mengisi $_FILES pada POST; index.php meneruskannya sebagai PUT

    const tombol = document.querySelector('#btn-simpan');
    tombol.disabled = true;
    try {
        const hasil = await panggilApi(`/mahasiswa/${encodeURIComponent(id)}`, { method: 'POST', body: data });
        if (hasil) {
            toastSetelahPindah(hasil.message || 'Mahasiswa berhasil diperbarui');
            window.location.replace('dashboard.html');
        }
    } finally { tombol.disabled = false; }
}

if (!id || !/^\d+$/.test(id)) {
    toastSetelahPindah('ID mahasiswa tidak valid.', 'gagal');
    window.location.replace('dashboard.html');
} else {
    muatDataUntukEdit(id);
    form.addEventListener('submit', (e) => { e.preventDefault(); simpanPerubahan(id); });
}
