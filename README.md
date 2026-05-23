# 🎓 SIAKAD — Sistem Informasi Akademik
### Manajemen Data Mahasiswa (CRUD Web App)

---

## 📋 Fitur Lengkap

| Fitur | Detail |
|---|---|
| **CRUD** | Create, Read, Update, Delete data mahasiswa |
| **Database** | MySQL via Laravel Eloquent ORM |
| **OOP & Class** | Model, Controller, Service (SearchService, SortService) |
| **Import/Export** | Upload/Download CSV & JSON |
| **Search Algorithm** | Linear Search O(n), Binary Search O(log n), Sequential Search |
| **Sort Algorithm** | Bubble Sort, Selection Sort, Insertion Sort |
| **Validasi Regex** | NIM, Nama, Email, No. HP |
| **Error Handling** | Try-Catch + notifikasi alert Bootstrap |
| **Time Complexity** | Estimasi + aktual waktu pencarian (ms & detik) |
| **Activity Log** | Build log semua aksi + komentar user |
| **Login System** | Autentikasi dengan validasi Regex |

---

## 🛠️ Instalasi & Setup

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js (opsional, untuk Vite)

### Langkah Instalasi

```bash
# 1. Extract project dari RAR, masuk ke folder
cd siakad

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate
```

### Konfigurasi Database MySQL

**Buat database dulu di MySQL:**
```sql
CREATE DATABASE siakad_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Edit file `.env`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=siakad_db
DB_USERNAME=root
DB_PASSWORD=           ← isi password MySQL kamu
```

### Jalankan Migrasi & Seeder

```bash
# Buat tabel di database
php artisan migrate

# Isi data awal (admin user + 20 mahasiswa dummy)
php artisan db:seed

# Atau sekaligus:
php artisan migrate --seed
```

### Jalankan Server

```bash
php artisan serve
```

Buka di browser: **http://localhost:8000**

---

## 🔑 Akun Login Default

| Role | Email | Password |
|---|---|---|
| Admin | admin@siakad.ac.id | password |
| Operator | operator@siakad.ac.id | password |

---

## 📁 Struktur Project

```
siakad/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php        ← Login/Logout
│   │   └── MahasiswaController.php   ← CRUD + Import/Export
│   ├── Models/
│   │   ├── Mahasiswa.php             ← OOP Model
│   │   ├── ActivityLog.php           ← Build Log
│   │   └── User.php
│   └── Services/
│       ├── SearchService.php         ← Linear/Binary/Sequential
│       └── SortService.php           ← Bubble/Selection/Insertion
├── database/
│   ├── migrations/                   ← Skema tabel MySQL
│   └── seeders/DatabaseSeeder.php    ← Data dummy
├── resources/views/
│   ├── layouts/app.blade.php         ← Layout utama
│   ├── auth/login.blade.php          ← Halaman login
│   ├── mahasiswa/
│   │   ├── index.blade.php           ← Daftar + Search + Sort
│   │   ├── create.blade.php          ← Form tambah
│   │   ├── edit.blade.php            ← Form edit
│   │   ├── show.blade.php            ← Detail
│   │   └── import.blade.php          ← Upload CSV/JSON
│   └── logs/index.blade.php          ← Activity log
├── routes/web.php                    ← Semua route
├── .env.example                      ← Konfigurasi
└── composer.json
```

---

## 📊 Algoritma yang Diimplementasikan

### Search
| Algoritma | Kompleksitas | Keterangan |
|---|---|---|
| Linear Search | O(n) | Cari satu-satu, cocok untuk semua data |
| Binary Search | O(log n) | Data harus terurut, sangat cepat |
| Sequential Search | O(n) | Linear + early exit untuk data terurut |

### Sort
| Algoritma | Best | Average | Worst |
|---|---|---|---|
| Bubble Sort | O(n) | O(n²) | O(n²) |
| Selection Sort | O(n²) | O(n²) | O(n²) |
| Insertion Sort | O(n) | O(n²) | O(n²) |

---

## 🌐 Hosting ke InfinityFree / cPanel

1. Upload semua file ke `public_html/`
2. Pindahkan isi folder `public/` ke root `public_html/`
3. Edit `index.php` di root — ubah path ke `bootstrap/app.php`
4. Buat database di cPanel → isi kredensial di `.env`
5. Import SQL dari `php artisan migrate` via PhpMyAdmin

---

## 📦 Format Import CSV

```csv
nim,nama,email,no_hp,prodi,fakultas,angkatan,status,ipk,alamat
12345678,Budi Santoso,budi@email.com,081234567890,Teknik Informatika,Ilmu Komputer,2022,aktif,3.75,Jl. Contoh
```

## 📦 Format Import JSON

```json
[
  {
    "nim": "12345678",
    "nama": "Budi Santoso",
    "email": "budi@email.com",
    "prodi": "Teknik Informatika",
    "fakultas": "Ilmu Komputer",
    "angkatan": 2022,
    "status": "aktif",
    "ipk": 3.75
  }
]
```

---

*© 2024 SIAKAD — Project UAS Manajemen Data Mahasiswa*
