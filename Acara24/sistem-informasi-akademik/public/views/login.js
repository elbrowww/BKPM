// BASE_URL dihitung otomatis dari lokasi halaman (.../public/views/xxx.html -> .../public)
const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];

// Jika sudah login, langsung ke dashboard
if (localStorage.getItem('token')) {
    window.location.href = 'dashboard.html';
}

document.querySelector('#form-login').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const res = await fetch(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username: username.value, password: password.value }),
        });
        const hasil = await res.json();
        if (hasil.success) {
            localStorage.setItem('token', hasil.data.token);
            localStorage.setItem('role', hasil.data.role);
            window.location.href = 'dashboard.html';
        } else {
            alert(hasil.message);
        }
    } catch (error) {
        console.error('Gagal login:', error);
        alert('Tidak dapat menghubungi server');
    }
});
