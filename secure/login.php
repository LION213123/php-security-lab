<?php
// ============================================================
// secure/login.php - SQL Injection SECURE Demo
// Menggunakan PDO Prepared Statement dengan Parameter Binding
// ============================================================

require_once '../config/database.php';

$result_msg    = '';
$result_class  = '';
$user_data     = null;
$payload_test  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Deteksi apakah input mengandung payload SQL Injection (untuk edukasi)
    $sqli_patterns = ["'", '"', '--', '/*', '*/', 'OR', 'AND', 'UNION', 'DROP', 'SELECT', '='];
    foreach ($sqli_patterns as $p) {
        if (stripos($username, $p) !== false || stripos($password, $p) !== false) {
            $payload_test = true;
            break;
        }
    }

    // ✅ SECURE: Menggunakan PDO Prepared Statement
    // Input tidak pernah digabungkan langsung ke query!
    $pdo = getPDO();

    // Template query — tanda ? adalah placeholder, BUKAN nilai langsung
    $sql  = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $pdo->prepare($sql);

    // Binding parameter — PDO yang menangani escaping secara internal
    $stmt->execute([$username, $password]);
    $user_data = $stmt->fetch();

    if ($user_data) {
        $result_msg   = "✅ LOGIN BERHASIL! Selamat datang, <strong>" . htmlspecialchars($user_data['username'], ENT_QUOTES, 'UTF-8') . "</strong>";
        $result_class = 'success';
    } else {
        $result_msg   = "❌ Login gagal. Username atau password salah (atau payload SQL Injection ditolak).";
        $result_class = 'error';
        $user_data    = null;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[SECURE] SQL Injection Protected :: PHP Security Lab</title>
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
                <span class="header-title">root@security-lab :: secure/login.php</span>
            </div>
        </header>

        <a href="../index.php" class="back-btn">← kembali ke menu utama</a>

        <!-- PAGE TITLE -->
        <div class="page-badge secure-badge">✅ SECURE — SQL INJECTION PROTECTED</div>
        <h1 class="page-title">SQL Injection: <span class="secure-text">Secure Login</span></h1>

        <!-- SECURE EXPLANATION -->
        <div class="secure-box">
            <div class="box-title">🟢 [SECURE] Bagaimana Halaman Ini Dilindungi?</div>
            <p>Query menggunakan <strong>PDO Prepared Statement dengan Parameter Binding</strong>:</p>
            <div class="code-block">
                <span class="code-comment">// ✅ CARA YANG BENAR — Prepared Statement</span><br>
                $sql  = <span class="code-string">"SELECT * FROM users WHERE username = <span class="code-safe">?</span> AND password = <span class="code-safe">?</span>"</span>;<br>
                $stmt = $pdo->prepare($sql);<br>
                <span class="code-comment">// Input dikirim terpisah sebagai parameter — bukan digabung ke query!</span><br>
                $stmt->execute([<span class="code-safe">$username</span>, <span class="code-safe">$password</span>]);
            </div>
            <p>Tanda <code>?</code> adalah <em>placeholder</em>. PDO memastikan nilai yang dimasukkan <strong>tidak pernah diinterpretasikan sebagai perintah SQL</strong>, melainkan murni sebagai data.</p>
            <p>⚠️ <em>Catatan Produksi</em>: Di aplikasi nyata, password harus disimpan dengan <code>password_hash()</code> dan diverifikasi dengan <code>password_verify()</code>. Demo ini menggunakan plain text untuk kemudahan pembelajaran.</p>
        </div>

        <!-- LOGIN FORM -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>user@lab:~/secure$ ./login --protected</span>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">&gt; username:</label>
                    <input type="text" name="username" class="form-input secure-input"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '' ?>"
                           placeholder="masukkan username..." autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">&gt; password:</label>
                    <input type="text" name="password" class="form-input secure-input"
                           value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') : '' ?>"
                           placeholder="masukkan password..." autocomplete="off">
                    <small class="form-hint">(input password ditampilkan teks biasa untuk keperluan demo)</small>
                </div>
                <button type="submit" class="btn-secure-execute">🛡️ EXECUTE SECURE LOGIN</button>
            </form>
        </div>

        <!-- PREPARED STATEMENT VISUALIZATION -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>QUERY TEMPLATE (Prepared Statement):</span>
            </div>
            <div class="query-display secure-query">
                <span class="query-label">TEMPLATE &gt;&gt;</span> SELECT * FROM users WHERE username = <span class="code-safe">?</span> AND password = <span class="code-safe">?</span>
            </div>
            <div class="query-explain">
                <span class="secure-icon">🔒</span> Parameter binding memisahkan <strong>struktur query</strong> dari <strong>data input</strong>.<br>
                Input pengguna tidak pernah dieksekusi sebagai SQL — hanya dikirim sebagai nilai literal.
            </div>
        </div>

        <!-- PAYLOAD DETECTION & RESULT -->
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

        <?php if ($payload_test): ?>
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>PAYLOAD ANALYSIS:</span>
            </div>
            <div class="payload-detected">
                ⚡ Payload SQL Injection terdeteksi dalam input!<br>
                <small>Input: <code><?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></small><br><br>
                <strong>Dengan Prepared Statement, payload ini TIDAK mengubah logika query.</strong><br>
                Input diperlakukan murni sebagai string literal "username" yang dicari di database.<br>
                Karena tidak ada user dengan username tersebut secara literal, login <strong>gagal dengan benar</strong>.
            </div>
        </div>
        <?php endif; ?>

        <div class="result-box <?= $result_class ?>">
            <?= $result_msg ?>
        </div>
        <?php endif; ?>

        <!-- HOW IT WORKS COMPARISON -->
        <div class="edu-section">
            <div class="edu-title">🔬 Perbandingan: Unsecure vs Secure</div>
            <div class="compare-grid">
                <div class="compare-col danger-col">
                    <div class="compare-title">❌ UNSECURE (Vulnerable)</div>
                    <div class="code-block small-code">
                        $q = "SELECT * FROM users<br>
                        &nbsp;WHERE username='<span class="code-danger">$username</span>'";<br>
                        <br>
                        <span class="code-comment">// Input: ' OR '1'='1</span><br>
                        <span class="code-comment">// Query jadi:</span><br>
                        <span class="code-danger">...WHERE username=''<br>
                        OR '1'='1'</span><br>
                        <span class="code-comment">// Selalu TRUE! Login bypass!</span>
                    </div>
                </div>
                <div class="compare-col secure-col">
                    <div class="compare-title">✅ SECURE (Protected)</div>
                    <div class="code-block small-code">
                        $stmt = $pdo->prepare(<br>
                        &nbsp;"SELECT * FROM users<br>
                        &nbsp;WHERE username = <span class="code-safe">?</span>");<br>
                        $stmt->execute([<span class="code-safe">$username</span>]);<br>
                        <br>
                        <span class="code-comment">// Input: ' OR '1'='1</span><br>
                        <span class="code-comment">// Dikirim SEBAGAI DATA LITERAL</span><br>
                        <span class="code-safe">// Tidak ada user bernama " ' OR '1'='1"</span><br>
                        <span class="code-safe">// Login gagal dengan benar ✓</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CREDENTIAL HINT -->
        <div class="credential-hint">
            <span class="prompt">sys@hint:~$</span> Kredensial valid:<br>
            <code>admin / admin123</code> &nbsp;|&nbsp; <code>mahasiswa / mahasiswa123</code>
        </div>

        <div class="nav-bottom">
            <a href="../unsecure/login.php" class="btn-danger">⚠ Lihat Versi RENTAN</a>
            <a href="../index.php" class="btn-back">← Kembali ke Menu</a>
        </div>

    </div>
    <script src="../assets/js/terminal.js"></script>
</body>
</html>
