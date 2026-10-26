
<?php
require_once __DIR__ . '/../includes/role.php';
require_role('mahasiswa');

$page_title = 'Profil Mahasiswa';
$user_id = (int)current_user()['id'];
$errors = [];

$stmt = $pdo->prepare(
    "SELECT nama, nim, email, username, no_telpon, fakultas, program_studi
     FROM users WHERE id = ? AND role = 'mahasiswa'"
);
$stmt->execute([$user_id]);
$profil = $stmt->fetch();

if (!$profil) {
    set_flash('error', 'Profil tidak ditemukan.');
    redirect('mahasiswa/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        set_flash('error', 'Token keamanan tidak valid.');
        redirect('mahasiswa/profil.php');
    }

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_telpon = trim($_POST['no_telpon'] ?? '');
    $fakultas = trim($_POST['fakultas'] ?? '');
    $program_studi = trim($_POST['program_studi'] ?? '');

    if ($nama === '') $errors[] = 'Nama wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id <> ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) $errors[] = 'Email sudah digunakan akun lain.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "UPDATE users
             SET nama = ?, email = ?, no_telpon = ?, fakultas = ?,
                 program_studi = ?, updated_at = NOW()
             WHERE id = ? AND role = 'mahasiswa'"
        );
        $stmt->execute([
            $nama, $email, $no_telpon ?: null,
            $fakultas ?: null, $program_studi ?: null, $user_id
        ]);
        $_SESSION['user']['nama'] = $nama;
        set_flash('success', 'Profil berhasil diperbarui.');
        redirect('mahasiswa/profil.php');
    }

    $profil = array_merge($profil, [
        'nama' => $nama, 'email' => $email, 'no_telpon' => $no_telpon,
        'fakultas' => $fakultas, 'program_studi' => $program_studi
    ]);
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar_mahasiswa.php';
?>

<section class="page-heading">
    <div><h1>Profil Mahasiswa</h1><p>Periksa dan perbarui informasi akun Anda.</p></div>
</section>

<?php if ($errors): ?>
    <div class="alert alert-error"><ul>
        <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
    </ul></div>
<?php endif; ?>

<section class="card">
    <form method="POST" data-validate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input class="form-control" id="nama" name="nama" required value="<?= e($profil['nama']) ?>">
        </div>
        <div class="form-group">
            <label for="nim">NIM</label>
            <input class="form-control" id="nim" value="<?= e($profil['nim'] ?? '') ?>" disabled>
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" value="<?= e($profil['username']) ?>" disabled>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?= e($profil['email']) ?>">
        </div>
        <div class="form-group">
            <label for="no_telpon">Nomor Telepon</label>
            <input class="form-control" id="no_telpon" name="no_telpon" value="<?= e($profil['no_telpon'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="fakultas">Fakultas</label>
            <input class="form-control" id="fakultas" name="fakultas" value="<?= e($profil['fakultas'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="program_studi">Program Studi</label>
            <input class="form-control" id="program_studi" name="program_studi" value="<?= e($profil['program_studi'] ?? '') ?>">
        </div>

        <button class="btn btn-primary" type="submit">Simpan Profil</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
