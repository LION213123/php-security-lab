# 🔐 PHP Security Lab
### SQL Injection & XSS Demo — Educational Use Only

```
 ____  _   _ ____     ____  _____ ____ _   _ ____  ___ _______   __  _        _    ____
|  _ \| | | |  _ \   / ___||  ___/ ___| | | |  _ \|_ _|_   _\ \ / / | |      / \  | __ )
| |_) | |_| | |_) |  \___ \| |_ | |   | | | | |_) || |  | |  \ V /  | |     / _ \ |  _ \
|  __/|  _  |  __/    ___) |  _|| |___| |_| |  _ < | |  | |   | |   | |___ / ___ \| |_) |
|_|   |_| |_|_|      |____/|_|   \____|\___/|_| \_\___| |_|   |_|   |_____/_/   \_\____/
```

---

## ⚠️ Peringatan Etika

> **Lab ini HANYA untuk pembelajaran lokal.**
> Jangan gunakan teknik yang dipelajari di sini untuk menyerang website nyata.
> Melakukan serangan siber tanpa izin adalah **tindakan ilegal** di Indonesia (UU ITE)
> dan di negara manapun di dunia.

---

## 📋 Deskripsi Project

PHP Security Lab adalah aplikasi web edukasi yang mendemonstrasikan perbedaan antara
aplikasi web yang **rentan** dan **aman** terhadap dua jenis serangan paling umum:

| Serangan | Versi Rentan | Versi Aman |
|---|---|---|
| SQL Injection | `unsecure/login.php` | `secure/login.php` |
| Cross-Site Scripting (XSS) | `unsecure/comment.php` | `secure/comment.php` |

---

## 🗂️ Struktur Folder

```
php-security-lab/
├── index.php                   # Halaman utama / menu navigasi
├── config/
│   └── database.php            # Konfigurasi koneksi database (MySQLi + PDO)
├── database/
│   └── security_lab.sql        # Script SQL untuk setup database
├── unsecure/
│   ├── login.php               # Demo SQL Injection RENTAN
│   └── comment.php             # Demo XSS RENTAN
├── secure/
│   ├── login.php               # Demo SQL Injection AMAN
│   └── comment.php             # Demo XSS AMAN
├── assets/
│   ├── css/
│   │   └── hacker-style.css    # Stylesheet tema terminal retro
│   └── js/
│       └── terminal.js         # Efek terminal (typing, glitch, cursor trail)
└── README.md                   # Dokumentasi ini
```

---

## 🚀 Cara Menjalankan

### Di XAMPP

1. **Download dan install XAMPP** dari https://www.apachefriends.org/
2. **Copy folder project** ke direktori htdocs:
   ```
   C:\xampp\htdocs\php-security-lab\
   ```
3. **Jalankan Apache dan MySQL** dari XAMPP Control Panel
4. **Import database** (lihat langkah selanjutnya)
5. **Buka browser** dan akses:
   ```
   http://localhost/php-security-lab/
   ```

### Di Laragon

1. **Download dan install Laragon** dari https://laragon.org/
2. **Copy folder project** ke direktori www:
   ```
   C:\laragon\www\php-security-lab\
   ```
3. **Start All** dari Laragon
4. **Import database** (lihat langkah selanjutnya)
5. **Buka browser** dan akses:
   ```
   http://localhost/php-security-lab/
   # atau jika Laragon auto-vhost aktif:
   http://php-security-lab.test/
   ```

---

## 🗄️ Cara Import Database

### Via phpMyAdmin (Direkomendasikan)

1. Buka `http://localhost/phpmyadmin`
2. Klik **"New"** / **"Baru"** di sidebar kiri
3. Buat database bernama `security_lab` (atau biarkan script SQL yang membuat)
4. Klik tab **"Import"**
5. Pilih file `database/security_lab.sql`
6. Klik **"Go"** / **"Kirim"**

### Via MySQL Command Line

```bash
# Login ke MySQL
mysql -u root -p

# Jalankan script SQL
source C:/xampp/htdocs/php-security-lab/database/security_lab.sql
# atau di Linux/Mac:
source /var/www/html/php-security-lab/database/security_lab.sql
```

---

## 🌐 URL Akses Demo

| Halaman | URL |
|---|---|
| Menu Utama | `http://localhost/php-security-lab/` |
| SQL Injection Rentan | `http://localhost/php-security-lab/unsecure/login.php` |
| SQL Injection Aman | `http://localhost/php-security-lab/secure/login.php` |
| XSS Rentan | `http://localhost/php-security-lab/unsecure/comment.php` |
| XSS Aman | `http://localhost/php-security-lab/secure/comment.php` |

---

## 🎯 Cara Demo SQL Injection (Versi Rentan)

Buka `http://localhost/php-security-lab/unsecure/login.php`

### Payload Login Normal (untuk perbandingan)
```
Username: admin
Password: admin123
→ Login berhasil normal
```

### Payload Bypass Authentication
```
Username: ' OR '1'='1
Password: apapun
→ Login BERHASIL tanpa password benar!
  Query menjadi: ... WHERE username='' OR '1'='1' AND password='...'
  Kondisi OR '1'='1' selalu TRUE → bypass autentikasi
```

```
Username: admin'--
Password: (apapun atau kosong)
→ Login BERHASIL sebagai admin tanpa password!
  Query menjadi: ... WHERE username='admin'--' AND password='...'
  Tanda -- adalah komentar SQL → sisa query (AND password=...) diabaikan!
```

```
Username: ' OR '1'='1'--
Password: (kosong)
→ Login BERHASIL sebagai user pertama di database
  Payload paling umum untuk bypass autentikasi universal
```

### Penjelasan Teknis
Query yang terbentuk (VULNERABLE):
```sql
SELECT * FROM users WHERE username = '' OR '1'='1' AND password = 'apapun'
```
Karena `'1'='1'` selalu benar, kondisi WHERE selalu terpenuhi → semua baris dikembalikan.

---

## ✅ Cara Membuktikan Versi Secure Aman (SQL Injection)

Buka `http://localhost/php-security-lab/secure/login.php`

Coba payload yang sama:
```
Username: ' OR '1'='1
Password: apapun
→ Login GAGAL dengan benar!
  Tidak ada user bernama "' OR '1'='1" di database.
```

**Mengapa aman?** PDO Prepared Statement memisahkan SQL dari data:
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
// Input dikirim sebagai data literal, bukan bagian dari SQL
```

---

## 🎯 Cara Demo XSS (Versi Rentan)

Buka `http://localhost/php-security-lab/unsecure/comment.php`

### Payload XSS Basic
```
Nama: Hacker
Komentar: <script>alert('XSS Berhasil!')</script>
→ Alert popup muncul saat halaman dimuat!
```

### Payload XSS via Event Handler
```
Komentar: <img src=x onerror=alert('XSS via img tag')>
→ Alert muncul karena src=x gagal dimuat, memicu onerror
```

### Payload Cookie Stealing (simulasi)
```
Komentar: <script>alert('Cookie: ' + document.cookie)</script>
→ Menampilkan isi cookie pengguna — di dunia nyata ini bisa dikirim ke server penyerang!
```

### Payload DOM Manipulation
```
Komentar: <h1 style="color:red">HALAMAN INI TELAH DIHACKED</h1>
→ Mengubah tampilan halaman yang dilihat pengunjung lain
```

---

## ✅ Cara Membuktikan Versi Secure Aman (XSS)

Buka `http://localhost/php-security-lab/secure/comment.php`

Coba payload yang sama:
```
Komentar: <script>alert('XSS')</script>
→ Komentar muncul sebagai TEKS BIASA: <script>alert('XSS')</script>
  Script TIDAK dieksekusi!
```

**Mengapa aman?** htmlspecialchars() mengubah karakter berbahaya menjadi entitas HTML:
```php
echo htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8');
// '<script>' → '&lt;script&gt;' (ditampilkan sebagai teks, bukan HTML aktif)
```

---

## 🔍 Perbedaan Versi Unsecure vs Secure

### SQL Injection

| Aspek | Unsecure | Secure |
|---|---|---|
| Query building | String concatenation langsung | PDO Prepared Statement |
| Input handling | Langsung ke query | Parameter binding (`?`) |
| SQL Injection | Bisa bypass autentikasi | Payload diperlakukan sebagai data |
| Kode | `$q = "... '$username' ..."` | `$stmt->execute([$username])` |

### Cross-Site Scripting (XSS)

| Aspek | Unsecure | Secure |
|---|---|---|
| Output display | `echo $row['comment']` | `echo htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8')` |
| Script execution | Dieksekusi browser | Ditampilkan sebagai teks biasa |
| HTML tags | Aktif dirender | Di-escape menjadi entitas |
| XSS | Script berjalan | Payload aman |

---

## 🛡️ Mitigasi yang Diimplementasikan

### SQL Injection Protection
- **PDO Prepared Statements** — memisahkan struktur SQL dari data
- **Parameter Binding** — nilai input tidak pernah diinterpretasikan sebagai SQL
- **PDO Error Mode** — exception mode untuk handling error yang lebih baik

### XSS Protection
- **htmlspecialchars()** — escape karakter HTML special (`< > " ' &`)
- **ENT_QUOTES** — escape baik single maupun double quotes
- **UTF-8 encoding** — konsistensi charset untuk mencegah encoding bypass

### Best Practices (Untuk Produksi)
- Gunakan `password_hash()` dan `password_verify()` untuk password
- Implementasikan Content Security Policy (CSP) header
- Gunakan HTTPS
- Validasi input di server-side
- Implementasikan rate limiting
- Gunakan prepared statement untuk SEMUA query database

---

## 🔧 Konfigurasi

Edit `config/database.php` jika perlu menyesuaikan:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');       // Password MySQL Anda
define('DB_NAME', 'security_lab');
define('DB_PORT', 3306);
```

---

## 📚 Referensi Pembelajaran

- OWASP Top 10: https://owasp.org/www-project-top-ten/
- SQL Injection Prevention: https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html
- XSS Prevention: https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html
- PHP PDO: https://www.php.net/manual/en/book.pdo.php
- htmlspecialchars(): https://www.php.net/manual/en/function.htmlspecialchars.php

---

## 📝 Catatan Etika Keamanan

1. **Hanya di localhost** — Jangan deploy project ini ke server publik
2. **Izin tertulis** — Selalu dapatkan izin sebelum melakukan pengujian keamanan
3. **Responsible disclosure** — Jika menemukan kerentanan di sistem lain, laporkan ke pemiliknya
4. **Bug bounty** — Ikuti program resmi jika ingin berlatih di lingkungan yang legal
5. **Ethical hacking** — Gunakan ilmu ini untuk membangun sistem yang lebih aman, bukan merusaknya

---

*PHP Security Lab — For Educational Use Only | Local Deployment Only*
