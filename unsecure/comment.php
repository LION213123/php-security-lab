<?php
// ============================================================
// unsecure/comment.php - XSS VULNERABLE Demo
// PERINGATAN: Kode ini SENGAJA rentan untuk tujuan edukasi.
// JANGAN gunakan pola ini di aplikasi nyata!
// ============================================================

require_once '../config/database.php';

$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $_POST['name']    ?? '';
    $comment = $_POST['comment'] ?? '';

    if (!empty($name) && !empty($comment)) {
        $conn = getConnection();

        // ⚠️ VULNERABLE: Input langsung dimasukkan tanpa sanitasi
        // Tidak ada filtering atau escaping sama sekali!
        $name_escaped    = $conn->real_escape_string($name);    // hanya escape untuk SQL
        $comment_escaped = $conn->real_escape_string($comment); // tapi OUTPUT tidak di-escape!

        $sql = "INSERT INTO comments (name, comment) VALUES ('$name_escaped', '$comment_escaped')";

        if ($conn->query($sql)) {
            $success_msg = "Komentar berhasil disimpan!";
        } else {
            $error_msg = "Gagal menyimpan: " . $conn->error;
        }
        $conn->close();
    } else {
        $error_msg = "Nama dan komentar tidak boleh kosong.";
    }
}

// Ambil semua komentar dari database
$conn     = getConnection();
$comments = $conn->query("SELECT * FROM comments ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[VULNERABLE] XSS Demo :: PHP Security Lab</title>
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
                <span class="header-title">root@security-lab :: unsecure/comment.php</span>
            </div>
        </header>

        <a href="../index.php" class="back-btn">← kembali ke menu utama</a>

        <!-- PAGE TITLE -->
        <div class="page-badge danger-badge">⚠ VULNERABLE — XSS DEMO</div>
        <h1 class="page-title">Cross-Site Scripting: <span class="danger-text">Unsecure Comment</span></h1>

        <!-- DANGER EXPLANATION -->
        <div class="warning-box">
            <div class="box-title">🔴 [VULNERABLE] Mengapa Halaman Ini Rentan?</div>
            <p>Komentar dari database <strong>ditampilkan langsung ke HTML tanpa encoding</strong>:</p>
            <div class="code-block">
                <span class="code-comment">// ❌ JANGAN LAKUKAN INI!</span><br>
                echo <span class="code-string">"&lt;p&gt;"</span> . <span class="code-danger">$row['comment']</span> . <span class="code-string">"&lt;/p&gt;"</span>;
                <span class="code-comment">// Output tidak di-escape, script akan dieksekusi browser!</span>
            </div>
            <p>Jika pengguna menyimpan komentar berisi <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code>, browser akan <strong>mengeksekusinya sebagai JavaScript</strong>, bukan menampilkannya sebagai teks.</p>
        </div>

        <!-- COMMENT FORM -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>user@lab:~/unsecure$ ./post_comment</span>
            </div>

            <?php if ($success_msg): ?>
            <div class="result-box success"><?= $success_msg ?></div>
            <?php endif; ?>
            <?php if ($error_msg): ?>
            <div class="result-box error"><?= $error_msg ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">&gt; nama:</label>
                    <input type="text" name="name" class="form-input" placeholder="nama kamu..." autocomplete="off"
                           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '' ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">&gt; komentar:</label>
                    <textarea name="comment" class="form-input form-textarea" placeholder="tulis komentar... (coba masukkan payload XSS!)"><?= isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
                </div>
                <button type="submit" class="btn-execute">▶ SUBMIT COMMENT</button>
            </form>
        </div>

        <!-- XSS PAYLOADS TUTORIAL -->
        <div class="edu-section">
            <div class="edu-title">🎯 Coba Payload XSS Berikut (Edukasi):</div>
            <div class="attack-table">
                <div class="attack-row header-row">
                    <span>Payload</span>
                    <span>Efek</span>
                </div>
                <div class="attack-row">
                    <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code>
                    <span class="attack-effect">Popup alert — XSS basic berhasil</span>
                </div>
                <div class="attack-row">
                    <code>&lt;script&gt;alert(document.cookie)&lt;/script&gt;</code>
                    <span class="attack-effect">Tampilkan session cookie pengguna</span>
                </div>
                <div class="attack-row">
                    <code>&lt;img src=x onerror=alert('img XSS')&gt;</code>
                    <span class="attack-effect">XSS via atribut HTML event handler</span>
                </div>
                <div class="attack-row">
                    <code>&lt;marquee&gt;XSS DOM Manipulation&lt;/marquee&gt;</code>
                    <span class="attack-effect">Manipulasi tampilan halaman</span>
                </div>
            </div>
            <div class="how-it-works">
                <div class="edu-title">⚡ Dampak XSS di Dunia Nyata:</div>
                <ul class="impact-list">
                    <li>🍪 <strong>Session Hijacking</strong> — Penyerang mencuri cookie pengguna untuk mengambil alih akun</li>
                    <li>🎣 <strong>Phishing</strong> — Menampilkan form login palsu di dalam halaman asli</li>
                    <li>🖥️ <strong>Defacement</strong> — Mengubah tampilan halaman yang dilihat pengguna lain</li>
                    <li>🕵️ <strong>Keylogging</strong> — Merekam setiap ketikan pengguna</li>
                    <li>🔗 <strong>Redirect</strong> — Mengarahkan pengguna ke website berbahaya</li>
                </ul>
            </div>
        </div>

        <!-- COMMENT LIST -->
        <div class="terminal-card">
            <div class="card-header-bar">
                <span class="dot dot-red"></span>
                <span class="dot dot-yellow"></span>
                <span class="dot dot-green"></span>
                <span>OUTPUT KOMENTAR (tanpa sanitasi):</span>
            </div>
            <div class="comments-list">
                <?php if ($comments && $comments->num_rows > 0): ?>
                    <?php while ($row = $comments->fetch_assoc()): ?>
                    <div class="comment-item unsecure-comment">
                        <div class="comment-header">
                            <span class="comment-author">
                                <!-- ⚠️ VULNERABLE: nama tidak di-escape! -->
                                [<?= $row['name'] ?>]
                            </span>
                            <span class="comment-time"><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="comment-body">
                            <!-- ⚠️ VULNERABLE: komentar tidak di-escape! Script akan dieksekusi! -->
                            <?= $row['comment'] ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-comments">Belum ada komentar. Jadilah yang pertama!</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-bottom">
            <a href="../secure/comment.php" class="btn-secure">▶ Lihat Versi AMAN →</a>
            <a href="../index.php" class="btn-back">← Kembali ke Menu</a>
        </div>

    </div>
    <script src="../assets/js/terminal.js"></script>
</body>
</html>
