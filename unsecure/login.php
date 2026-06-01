<?php
// ============================================================
// unsecure/login.php - SQL Injection VULNERABLE Demo
// PERINGATAN: Kode ini SENGAJA rentan untuk tujuan edukasi.
// JANGAN gunakan pola ini di aplikasi nyata!
// ============================================================

require_once '../config/database.php';

$query_shown  = '';
$result_msg   = '';
$result_class = '';
$user_data    = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ⚠️ VULNERABLE: Input langsung digabungkan ke query SQL
    // Ini adalah praktik yang SANGAT BERBAHAYA!
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $query_shown = $query;

    $conn   = getConnection();
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $user_data    = $result->fetch_assoc();
        $result_msg   = "✅ LOGIN BERHASIL! Selamat datang, <strong>" . $user_data['username'] . "</strong>";
        $result_class = 'success';
    } else {
        if ($conn->error) {
            $result_msg   = "❌ QUERY ERROR: " . $conn->error;
            $result_class = 'error';
        } else {
            $result_msg   = "❌ Login gagal. Username atau password salah.";
            $result_class = 'error';
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[VULNERABLE] SQL Injection Demo :: PHP Security Lab</title>
    <link rel="stylesheet" href="../assets/css/hacker-style.css">
</head>
<body>
    <div class="scanline"></div>
    <div class="container">

        <!-- HEADER -->
        <header class="terminal-header">
            <div class="header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span class="header-title">root@security-lab :: unsecure/login.php</span>
            </div>
        </header>

        <a href="../index.php" class="back-btn">← kembali ke menu utama</a>

        <!-- PAGE TITLE -->
        <div class="page-badge danger-badge">⚠ VULNERABLE — SQL INJECTION DEMO</div>
        <h1 class="page-title">SQL Injection: <span class="danger-text">Unsecure Login</span></h1>

        <!-- DANGER EXPLANATION -->
        <div class="warning-box">
            <div class="box-title">🔴 [VULNERABLE] Mengapa Halaman Ini Rentan?</div>
            <p>Query SQL dibentuk dengan <strong>menggabungkan input pengguna langsung ke string query</strong>:</p>
            <div class="code-block">
                <span class="code-comment">// ❌ JANGAN LAKUKAN INI!</span><br>
                $query = <span class="code-string">"SELECT * FROM users WHERE username = '<span class="code-danger">$username</span>' AND password = '<span class="code-danger">$password</span>'"</span>;
            </div>
            <p>Seorang penyerang dapat memasukkan karakter <code>'</code> dan perintah SQL tambahan untuk <strong>mengubah logika query</strong>, memungkinkan bypass autentikasi tanpa mengetahui password yang benar.</p>
        </div>

        <!-- LOGIN FORM -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>user@lab:~/unsecure$ ./login</span>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">&gt; username:</label>
                    <input type="text" name="username" class="form-input"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '' ?>"
                           placeholder="masukkan username..." autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">&gt; password:</label>
                    <input type="text" name="password" class="form-input"
                           value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : '' ?>"
                           placeholder="masukkan password..." autocomplete="off">
                    <small class="form-hint">(input password ditampilkan teks biasa untuk keperluan demo)</small>
                </div>
                <button type="submit" class="btn-execute">▶ EXECUTE LOGIN</button>
            </form>
        </div>

        <!-- QUERY DISPLAY -->
        <?php if ($query_shown): ?>
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>QUERY YANG TERBENTUK:</span>
            </div>
            <div class="query-display <?= (str_contains($query_shown, "OR") || str_contains($query_shown, "--")) ? 'query-injected' : '' ?>">
                <span class="query-label">SQL &gt;&gt;</span> <?= htmlspecialchars($query_shown, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php if (str_contains($query_shown, "OR '1'='1") || str_contains($query_shown, "--")): ?>
            <div class="inject-warning">
                ⚡ PAYLOAD TERDETEKSI! Query telah dimanipulasi oleh input pengguna!
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- RESULT -->
        <?php if ($result_msg): ?>
        <div class="result-box <?= $result_class ?>">
            <?= $result_msg ?>
            <?php if ($user_data): ?>
            <div class="user-data">
                <br>📋 Data user yang didapat dari database:<br>
                <code>ID: <?= $user_data['id'] ?> | Username: <?= htmlspecialchars($user_data['username'], ENT_QUOTES, 'UTF-8') ?></code>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ATTACK TUTORIAL -->
        <div class="edu-section">
            <div class="edu-title">🎯 Coba Serangan SQL Injection (Edukasi):</div>

            <div class="attack-table">
                <div class="attack-row header-row">
                    <span>Username Input</span>
                    <span>Password</span>
                    <span>Efek</span>
                </div>
                <div class="attack-row">
                    <code>' OR '1'='1</code>
                    <span>apapun</span>
                    <span class="attack-effect">Bypass — login sebagai user pertama di DB</span>
                </div>
                <div class="attack-row">
                    <code>admin'--</code>
                    <span>apapun</span>
                    <span class="attack-effect">Bypass — komentar sisa query, login sebagai admin</span>
                </div>
                <div class="attack-row">
                    <code>' OR '1'='1'--</code>
                    <span>(kosong)</span>
                    <span class="attack-effect">Bypass universal — login sebagai user manapun</span>
                </div>
            </div>

            <div class="how-it-works">
                <div class="edu-title">🔬 Bagaimana Injeksi Terjadi?</div>
                <p>Ketika Anda mengetik <code>' OR '1'='1</code> sebagai username, query yang terbentuk menjadi:</p>
                <div class="code-block">
                    SELECT * FROM users WHERE username = '<span class="code-danger">' OR '1'='1</span>' AND password = '...'
                    <br><br>
                    <span class="code-comment">-- Setelah diparse oleh MySQL:</span><br>
                    SELECT * FROM users WHERE username = '' <span class="code-danger">OR '1'='1'</span>  AND password = '...'
                    <br><br>
                    <span class="code-comment">-- '1'='1' selalu TRUE, jadi WHERE selalu terpenuhi!</span>
                </div>
                <p>📌 Dampak nyata: <strong>Bypass autentikasi</strong>, akses data semua user, DROP TABLE, dan berbagai serangan lainnya.</p>
            </div>
        </div>

        <!-- CREDENTIAL HINT -->
        <div class="credential-hint">
            <span class="prompt">sys@hint:~$</span> Kredensial valid (untuk perbandingan login normal):<br>
            <code>admin / admin123</code> &nbsp;|&nbsp; <code>mahasiswa / mahasiswa123</code>
        </div>

        <div class="nav-bottom">
            <a href="../secure/login.php" class="btn-secure">▶ Lihat Versi AMAN →</a>
            <a href="../index.php" class="btn-back">← Kembali ke Menu</a>
        </div>

    </div>
    <script src="../assets/js/terminal.js"></script>
</body>
</html>
