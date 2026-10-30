// URL API dihitung otomatis dari lokasi folder public.
const BASE_URL = window.location.origin + window.location.pathname.split('/views/')[0];

if (localStorage.getItem('token')) window.location.replace('dashboard.html');

document.querySelector('#form-login').addEventListener('submit', async (event) => {
    event.preventDefault();
    const pesan = document.querySelector('#pesan');
    const username = document.querySelector('#username').value.trim();
    const password = document.querySelector('#password').value;
    pesan.textContent = 'Memeriksa akun...';
    try {
        const response = await fetch(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        const hasil = await response.json();
        if (!response.ok || !hasil.success) {
            pesan.textContent = hasil.message || 'Login gagal';
            return;
        }
        localStorage.setItem('token', hasil.data.token);
        localStorage.setItem('role', hasil.data.role);
        localStorage.setItem('username', hasil.data.username);
        window.location.replace('dashboard.html');
    } catch (error) {
        console.error(error);
        pesan.textContent = 'Tidak dapat menghubungi server';
    }
});
