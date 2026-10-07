<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Informasi Server PHP</title>
</head>
<body>

    <h2>Tugas Mandiri - Informasi Sistem PHP</h2>
    
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Informasi</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>Nama Anda</td>
            <td>Nama Lengkap Anda</td>
        </tr>
        <tr>
            <td>NIM</td>
            <td>12345678</td>
        </tr>
        <tr>
            <td>Waktu server</td>
            <td>
                <?php 
                date_default_timezone_set('Asia/Jakarta');
                echo date('d-m-Y H:i:s'); 
                ?>
            </td>
        </tr>
        <tr>
            <td>Versi PHP</td>
            <td><?php echo phpversion(); ?></td>
        </tr>
        <tr>
            <td>Sistem operasi server</td>
            <td><?php echo PHP_OS; ?></td>
        </tr>
    </table>

</body>
</html>