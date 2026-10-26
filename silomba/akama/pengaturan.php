
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('akama');

$page_title = 'Pengaturan';

try {
    $pdo->exec(
        "INSERT INTO pengaturan (id, nama_situs, email_admin)
         VALUES (1, 'SILOMBA', '')
         ON DUPLICATE KEY UPDATE id = id"
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            set_flash('error', 'Token keamanan tidak valid.');
            redirect('akama/pengaturan.php');
        }

        $nama_situs = trim($_POST['nama_situs'] ?? '');
        $email_admin = trim($_POST['email_admin'] ?? '');

        if ($nama_situs === '' || mb_strlen($nama_situs) > 100) {
            set_flash('error', 'Nama sistem wajib diisi maksimal 100 karakter.');
            redirect('akama/pengaturan.php');
        }

        if ($email_admin !== '' && !filter_var($email_admin, FILTER_VALIDATE_EMAIL)) {
            set_flash('error', 'Format email administrator tidak valid.');
            redirect('akama/pengaturan.php');
        }

        $stmt = $pdo->prepare(
            "UPDATE pengaturan
             SET nama_situs = :nama, email_admin = :email, updated_at = NOW()
             WHERE id = 1"
        );

        $stmt->execute([
            'nama' => $nama_situs,
            'email' => $email_admin
        ]);

        set_flash('success', 'Pengaturan sistem berhasil disimpan.');
        redirect('akama/pengaturan.php');
    }

    $pengaturan = $pdo->query(
        "SELECT * FROM pengaturan WHERE id = 1"
    )->fetch();

    if (!$pengaturan) {
        $pengaturan = [
            'nama_situs' => 'SILOMBA',
            'email_admin' => ''
        ];
    }
} catch (PDOException $e) {
    $pengaturan = [
        'nama_situs' => 'SILOMBA',
        'email_admin' => ''
    ];
    set_flash('error', 'Pengaturan gagal dimuat. Pastikan tabel pengaturan tersedia.');
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_akama.php';
?>

<section class="page-heading">
    <div>
        <h1>Pengaturan Sistem</h1>
        <p>Kelola informasi dasar aplikasi SILOMBA.</p>
    </div>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Informasi Umum</h2>
            <p>Informasi berikut disimpan di database aplikasi.</p>
        </div>
    </div>

    <form method="POST" data-validate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="nama_situs">Nama Sistem</label>
            <input
                type="text"
                id="nama_situs"
                name="nama_situs"
                class="form-control"
                value="<?= e($pengaturan['nama_situs']) ?>"
                maxlength="100"
                required>
        </div>

        <div class="form-group">
            <label for="email_admin">Email Administrator</label>
            <input
                type="email"
                id="email_admin"
                name="email_admin"
                class="form-control"
                value="<?= e($pengaturan['email_admin']) ?>"
                placeholder="admin@kampus.ac.id">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
        </div>
    </form>
</section>

<section class="card">
    <div class="card-header">
        <div>
            <h2>Informasi Aplikasi</h2>
            <p>Informasi dasar instalasi SILOMBA.</p>
        </div>
    </div>

    <div class="detail-panel">
        <p><strong>Nama aplikasi:</strong> SILOMBA</p>
        <p><strong>Versi:</strong> 1.0.0</p>
        <p><strong>Teknologi:</strong> PHP Native, MySQL, HTML, CSS, JavaScript</p>
        <p><strong>Institusi:</strong> UIN Imam Bonjol Padang</p>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
