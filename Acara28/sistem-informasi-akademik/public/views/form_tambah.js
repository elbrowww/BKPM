const form = document.querySelector('#form-tambah');
bersihkanErrorSaatMengetik(form);

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Validasi sisi View (lapisan tambahan di luar validasi Model/Controller)
    const okTeks = validasiWajibIsi(form, { nim: 'NIM', nama: 'Nama', prodi: 'Program Studi' });
    const okFoto = validasiFoto(form.querySelector('#foto'));
    if (!okTeks || !okFoto) { toast('Periksa kembali isian formulir.', 'gagal'); return; }

    const data = new FormData(form);   // multipart; jangan set Content-Type manual
    ['nim', 'nama', 'prodi'].forEach(k => data.set(k, data.get(k).trim()));

    const tombol = document.querySelector('#btn-simpan');
    tombol.disabled = true;
    try {
        const hasil = await panggilApi('/mahasiswa', { method: 'POST', body: data });
        if (hasil) {
            toastSetelahPindah(hasil.message || 'Mahasiswa berhasil ditambahkan');
            window.location.replace('dashboard.html');
        }
    } finally { tombol.disabled = false; }
});
