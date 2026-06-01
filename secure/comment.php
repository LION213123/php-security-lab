<?php
// ============================================================
// secure/comment.php - XSS SECURE Demo
// Menggunakan htmlspecialchars() untuk output encoding
// ============================================================

require_once '../config/database.php';

$success_msg  = '';
$error_msg    = '';
$payload_test = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name']    ?? '';
    $comment = $_POST['comment'] ?? '';

    // Deteksi apakah input mengandung payload XSS (untuk edukasi)
    $xss_patterns = ['<script', '<img', '<svg', '<iframe', 'onerror', 'onload', 'onclick', 'javascript:'];
    foreach ($xss_patterns as $p) {
        if (stripos($name, $p) !== false || stripos($comment, $p) !== false) {
            $payload_test = true;
            break;
        }
    }

    if (!empty($name) && !empty($comment)) {
        $pdo = getPDO();

        // ✅ SECURE: Gunakan prepared statement untuk menyimpan data
        $stmt = $pdo->prepare("INSERT INTO comments (name, comment) VALUES (?, ?)");
        $stmt->execute([$name, $comment]);

        $success_msg = "Komentar berhasil disimpan!";
    } else {
        $error_msg = "Nama dan komentar tidak boleh kosong.";
    }
}

// Ambil semua komentar dari database
$pdo      = getPDO();
$stmt     = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC");
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[SECURE] XSS Protected :: PHP Security Lab</title>
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
                <span class="header-title">root@security-lab :: secure/comment.php</span>
            </div>
        </header>

        <a href="../index.php" class="back-btn">← kembali ke menu utama</a>

        <!-- PAGE TITLE -->
        <div class="page-badge secure-badge">✅ SECURE — XSS PROTECTED</div>
        <h1 class="page-title">Cross-Site Scripting: <span class="secure-text">Secure Comment</span></h1>

        <!-- SECURE EXPLANATION -->
        <div class="secure-box">
            <div class="box-title">🟢 [SECURE] Bagaimana Halaman Ini Dilindungi?</div>
            <p>Semua output dari database di-encode menggunakan <strong>htmlspecialchars()</strong>:</p>
            <div class="code-block">
                <span class="code-comment">// ✅ CARA YANG BENAR — Output Encoding</span><br>
                echo htmlspecialchars(<span class="code-safe">$row['comment']</span>, ENT_QUOTES, <span class="code-string">'UTF-8'</span>);<br>
                <br>
                <span class="code-comment">// Tag HTML diubah menjadi entitas yang aman:</span><br>
                <span class="code-safe">&lt;script&gt;</span> → &amp;lt;script&amp;gt; (ditampilkan sebagai teks biasa)<br>
                <span class="code-safe">" ' &amp; &lt; &gt;</span> → semua di-escape dengan ENT_QUOTES
            </div>
            <p>Browser menerima <strong>entitas HTML</strong>, bukan tag HTML aktif. Script tidak akan pernah dieksekusi — hanya ditampilkan sebagai karakter teks biasa.</p>
            <p>🔑 <strong>Prinsip Dasar</strong>: Anggap semua input dari pengguna sebagai <em>tidak terpercaya</em>. Selalu escape output sesuai konteks tampilannya.</p>
        </div>

        <!-- COMMENT FORM -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>user@lab:~/secure$ ./post_comment --sanitized</span>
            </div>

            <?php if ($success_msg): ?>
            <div class="result-box success"><?= htmlspecialchars($success_msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
            <div class="result-box error"><?= htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">&gt; nama:</label>
                    <input type="text" name="name" class="form-input secure-input" placeholder="nama kamu..." autocomplete="off"
                           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">&gt; komentar:</label>
                    <textarea name="comment" class="form-input form-textarea secure-input" placeholder="tulis komentar... (coba payload XSS — akan tampil sebagai teks!)"><?= isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
                </div>
                <button type="submit" class="btn-secure-execute">🛡️ SUBMIT SECURE COMMENT</button>
            </form>
        </div>

        <!-- PAYLOAD DETECTION -->
        <?php if ($payload_test && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>XSS PAYLOAD ANALYSIS:</span>
            </div>
            <div class="payload-detected">
                ⚡ Payload XSS terdeteksi dalam input!<br>
                <small>Komentar: <code><?= htmlspecialchars($_POST['comment'] ?? '', ENT_QUOTES, 'UTF-8') ?></code></small><br><br>
                <strong>Dengan htmlspecialchars(), payload ini akan ditampilkan sebagai TEKS BIASA di bawah.</strong><br>
                Browser tidak akan mengeksekusinya sebagai JavaScript. Cek output komentar di bawah untuk membuktikannya!
            </div>
        </div>
        <?php endif; ?>

        <!-- OUTPUT SANITIZATION DEMO -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>DEMO OUTPUT ENCODING:</span>
            </div>
            <div class="encoding-demo">
                <div class="encode-row">
                    <span class="encode-label">Input pengguna:</span>
                    <code class="encode-input">&lt;script&gt;alert('XSS')&lt;/script&gt;</code>
                </div>
                <div class="encode-arrow">↓ htmlspecialchars() ↓</div>
                <div class="encode-row">
                    <span class="encode-label">HTML yang dikirim:</span>
                    <code class="encode-output">&amp;lt;script&amp;gt;alert('XSS')&amp;lt;/script&amp;gt;</code>
                </div>
                <div class="encode-arrow">↓ Browser render ↓</div>
                <div class="encode-row">
                    <span class="encode-label">Yang terlihat user:</span>
                    <code class="encode-result">&lt;script&gt;alert('XSS')&lt;/script&gt; ← teks biasa, aman!</code>
                </div>
            </div>
        </div>

        <!-- COMMENT LIST — SECURE OUTPUT -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>OUTPUT KOMENTAR (dengan htmlspecialchars — AMAN):</span>
            </div>
            <div class="comments-list">
                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $row): ?>
                    <div class="comment-item secure-comment">
                        <div class="comment-header">
                            <span class="comment-author">
                                <!-- ✅ SECURE: nama di-escape dengan htmlspecialchars -->
                                [<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>]
                            </span>
                            <span class="comment-time"><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="comment-body">
                            <!-- ✅ SECURE: komentar di-escape, script TIDAK akan dieksekusi -->
                            <?= htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="secure-indicator">🔒 output di-encode</div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-comments">Belum ada komentar. Jadilah yang pertama!</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- COMPARISON -->
        <div class="edu-section">
            <div class="edu-title">🔬 Perbandingan: Unsecure vs Secure Output</div>
            <div class="compare-grid">
                <div class="compare-col danger-col">
                    <div class="compare-title">❌ UNSECURE (XSS Executed)</div>
                    <div class="code-block small-code">
                        <span class="code-comment">// Output langsung tanpa escape:</span><br>
                        echo <span class="code-danger">$row['comment']</span>;<br>
                        <br>
                        <span class="code-comment">// Input: &lt;script&gt;alert(1)&lt;/script&gt;</span><br>
                        <span class="code-comment">// HTML dikirim: &lt;script&gt;alert(1)&lt;/script&gt;</span><br>
                        <span class="code-danger">// Browser: EKSEKUSI JS! Alert muncul!</span>
                    </div>
                </div>
                <div class="compare-col secure-col">
                    <div class="compare-title">✅ SECURE (XSS Neutralized)</div>
                    <div class="code-block small-code">
                        <span class="code-comment">// Output dengan htmlspecialchars:</span><br>
                        echo htmlspecialchars(<span class="code-safe">$row['comment']</span>,<br>
                        &nbsp;&nbsp;ENT_QUOTES, <span class="code-string">'UTF-8'</span>);<br>
                        <br>
                        <span class="code-comment">// Input: &lt;script&gt;alert(1)&lt;/script&gt;</span><br>
                        <span class="code-comment">// HTML dikirim: &amp;lt;script&amp;gt;...</span><br>
                        <span class="code-safe">// Browser: tampilkan sebagai TEKS ✓</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="nav-bottom">
            <a href="../unsecure/comment.php" class="btn-danger">⚠ Lihat Versi RENTAN</a>
            <a href="../index.php" class="btn-back">← Kembali ke Menu</a>
        </div>

    </div>
    <script src="../assets/js/terminal.js"></script>
</body>
</html>
