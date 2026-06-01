<?php
// ============================================================
// index.php - PHP Security Lab Landing Page
// For Educational Purposes Only
// ============================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Security Lab :: SQL Injection & XSS Demo</title>
    <link rel="stylesheet" href="assets/css/hacker-style.css">
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
                <span class="header-title">root@security-lab ~ terminal v1.0</span>
            </div>
            <div class="logo-ascii">
<pre class="ascii-art">
 ____  _   _ ____     ____  _____ ____ _   _ ____  ___ _______   __  _        _    ____
|  _ \| | | |  _ \   / ___||  ___/ ___| | | |  _ \|_ _|_   _\ \ / / | |      / \  | __ )
| |_) | |_| | |_) |  \___ \| |_ | |   | | | | |_) || |  | |  \ V /  | |     / _ \ |  _ \
|  __/|  _  |  __/    ___) |  _|| |___| |_| |  _ < | |  | |   | |   | |___ / ___ \| |_) |
|_|   |_| |_|_|      |____/|_|   \____|\___/|_| \_\___| |_|   |_|   |_____/_/   \_\____/
</pre>
            </div>
            <div class="subtitle-type" id="subtitle"></div>
        </header>

        <!-- WARNING BANNER -->
        <div class="warning-banner">
            <span class="blink">⚠</span>
            &nbsp;[PERINGATAN ETIKA]&nbsp;
            <span class="blink">⚠</span>
            <br>
            <small>Lab ini <strong>HANYA</strong> untuk pembelajaran lokal. Jangan gunakan teknik ini untuk menyerang website nyata. Hacking tanpa izin adalah tindakan ilegal.</small>
        </div>

        <!-- SYSTEM INFO -->
        <div class="sys-info">
            <span class="prompt">sys@info:~$</span> <span class="cmd">echo "PHP Security Lab - SQL Injection & XSS Demo"</span><br>
            <span class="output">PHP Security Lab - SQL Injection & XSS Demo</span><br>
            <span class="prompt">sys@info:~$</span> <span class="cmd">cat /etc/lab-description</span><br>
            <span class="output">
                Aplikasi ini mendemonstrasikan perbedaan antara aplikasi web yang <strong>RENTAN</strong> dan <strong>AMAN</strong><br>
                terhadap dua serangan paling umum: SQL Injection dan Cross-Site Scripting (XSS).<br>
                Pelajari, praktikkan, dan pahami — lalu terapkan mitigasi yang benar di project Anda.
            </span>
        </div>

        <!-- MENU GRID -->
        <div class="section-title">
            <span class="prompt">root@lab:~$</span> <span class="cmd">ls -la /demos/</span>
        </div>

        <div class="demo-grid">

            <!-- SQL INJECTION DEMOS -->
            <div class="demo-section">
                <div class="section-badge">SQL INJECTION</div>

                <a href="unsecure/login.php" class="demo-card unsecure-card">
                    <div class="card-header">
                        <span class="status-indicator danger"></span>
                        <span class="card-type">[VULNERABLE]</span>
                    </div>
                    <div class="card-icon">💀</div>
                    <div class="card-title">SQL Injection UNSECURE</div>
                    <div class="card-desc">Form login menggunakan query string concatenation langsung. Rentan terhadap bypass autentikasi dan data exfiltration.</div>
                    <div class="card-cmd">$ ./unsecure/login.php</div>
                </a>

                <a href="secure/login.php" class="demo-card secure-card">
                    <div class="card-header">
                        <span class="status-indicator safe"></span>
                        <span class="card-type">[SECURED]</span>
                    </div>
                    <div class="card-icon">🛡️</div>
                    <div class="card-title">SQL Injection SECURE</div>
                    <div class="card-desc">Form login yang sama menggunakan PDO Prepared Statement. Payload SQL Injection tidak akan dieksekusi sebagai logika query.</div>
                    <div class="card-cmd">$ ./secure/login.php</div>
                </a>
            </div>

            <!-- XSS DEMOS -->
            <div class="demo-section">
                <div class="section-badge">CROSS-SITE SCRIPTING (XSS)</div>

                <a href="unsecure/comment.php" class="demo-card unsecure-card">
                    <div class="card-header">
                        <span class="status-indicator danger"></span>
                        <span class="card-type">[VULNERABLE]</span>
                    </div>
                    <div class="card-icon">💀</div>
                    <div class="card-title">XSS UNSECURE</div>
                    <div class="card-desc">Form komentar yang menampilkan output tanpa sanitasi. Script berbahaya akan dieksekusi oleh browser pengunjung.</div>
                    <div class="card-cmd">$ ./unsecure/comment.php</div>
                </a>

                <a href="secure/comment.php" class="demo-card secure-card">
                    <div class="card-header">
                        <span class="status-indicator safe"></span>
                        <span class="card-type">[SECURED]</span>
                    </div>
                    <div class="card-icon">🛡️</div>
                    <div class="card-title">XSS SECURE</div>
                    <div class="card-desc">Form komentar yang menggunakan htmlspecialchars() saat output. Payload XSS ditampilkan sebagai teks biasa, tidak dieksekusi.</div>
                    <div class="card-cmd">$ ./secure/comment.php</div>
                </a>
            </div>

        </div>

        <!-- ATTACK CHEATSHEET -->
        <div class="cheatsheet">
            <div class="section-title">
                <span class="prompt">root@lab:~$</span> <span class="cmd">cat payload_cheatsheet.txt</span>
            </div>
            <div class="cheat-grid">
                <div class="cheat-item">
                    <div class="cheat-title">🔓 SQL Injection Bypass</div>
                    <code>Username: ' OR '1'='1'--</code><br>
                    <code>Username: admin'--</code><br>
                    <code>Password: apapun</code>
                </div>
                <div class="cheat-item">
                    <div class="cheat-title">⚡ XSS Basic Payload</div>
                    <code>&lt;script&gt;alert('XSS')&lt;/script&gt;</code><br>
                    <code>&lt;img src=x onerror=alert(1)&gt;</code><br>
                    <code>&lt;svg onload=alert(document.cookie)&gt;</code>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer class="terminal-footer">
            <span class="prompt">root@lab:~$</span> <span class="cmd">exit</span><br>
            <small class="output">PHP Security Lab &copy; <?= date('Y') ?> — For Educational Use Only | Local Deployment Only</small>
        </footer>

    </div>

    <script src="assets/js/terminal.js"></script>
</body>
</html>
