<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder
 * Mengisi database dengan data awal: admin user + 20 mahasiswa dummy
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Admin User ----
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@siakad.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Operator BAAK',
            'email'    => 'operator@siakad.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'operator',
        ]);

        // ---- Data Mahasiswa Dummy ----
        $mahasiswas = [
            ['nim'=>'22110001','nama'=>'Andi Firmansyah',    'email'=>'andi.f@student.ac.id',    'no_hp'=>'081234567801','prodi'=>'Teknik Informatika',  'fakultas'=>'Ilmu Komputer','angkatan'=>2022,'status'=>'aktif',  'ipk'=>3.85,'alamat'=>'Jl. Sudirman No. 1, Jakarta'],
            ['nim'=>'22110002','nama'=>'Budi Santoso',       'email'=>'budi.s@student.ac.id',    'no_hp'=>'081234567802','prodi'=>'Teknik Informatika',  'fakultas'=>'Ilmu Komputer','angkatan'=>2022,'status'=>'aktif',  'ipk'=>3.52,'alamat'=>'Jl. Thamrin No. 2, Jakarta'],
            ['nim'=>'21110003','nama'=>'Citra Dewi Lestari', 'email'=>'citra.d@student.ac.id',   'no_hp'=>'082345678903','prodi'=>'Sistem Informasi',     'fakultas'=>'Ilmu Komputer','angkatan'=>2021,'status'=>'aktif',  'ipk'=>3.91,'alamat'=>'Jl. Gatot Subroto No. 3'],
            ['nim'=>'21110004','nama'=>'Dian Prasetyo',      'email'=>'dian.p@student.ac.id',    'no_hp'=>'083456789004','prodi'=>'Sistem Informasi',     'fakultas'=>'Ilmu Komputer','angkatan'=>2021,'status'=>'cuti',   'ipk'=>2.75,'alamat'=>'Jl. Rasuna Said No. 4'],
            ['nim'=>'20110005','nama'=>'Eka Putri Wulandari','email'=>'eka.p@student.ac.id',     'no_hp'=>'084567890105','prodi'=>'Teknik Elektro',       'fakultas'=>'Teknik',       'angkatan'=>2020,'status'=>'aktif',  'ipk'=>3.45,'alamat'=>'Jl. Kuningan No. 5'],
            ['nim'=>'20110006','nama'=>'Fajar Nugroho',      'email'=>'fajar.n@student.ac.id',   'no_hp'=>'085678901206','prodi'=>'Teknik Elektro',       'fakultas'=>'Teknik',       'angkatan'=>2020,'status'=>'lulus',  'ipk'=>3.78,'alamat'=>'Jl. Casablanca No. 6'],
            ['nim'=>'23110007','nama'=>'Galih Saputra',      'email'=>'galih.s@student.ac.id',   'no_hp'=>'086789012307','prodi'=>'Manajemen',            'fakultas'=>'Ekonomi',      'angkatan'=>2023,'status'=>'aktif',  'ipk'=>3.20,'alamat'=>'Jl. MT Haryono No. 7'],
            ['nim'=>'23110008','nama'=>'Hana Kurniawati',    'email'=>'hana.k@student.ac.id',    'no_hp'=>'087890123408','prodi'=>'Manajemen',            'fakultas'=>'Ekonomi',      'angkatan'=>2023,'status'=>'aktif',  'ipk'=>3.65,'alamat'=>'Jl. Pancoran No. 8'],
            ['nim'=>'22110009','nama'=>'Ilham Hidayat',      'email'=>'ilham.h@student.ac.id',   'no_hp'=>'088901234509','prodi'=>'Akuntansi',            'fakultas'=>'Ekonomi',      'angkatan'=>2022,'status'=>'aktif',  'ipk'=>3.40,'alamat'=>'Jl. Kalibata No. 9'],
            ['nim'=>'21110010','nama'=>'Julia Rahayu',       'email'=>'julia.r@student.ac.id',   'no_hp'=>'089012345610','prodi'=>'Akuntansi',            'fakultas'=>'Ekonomi',      'angkatan'=>2021,'status'=>'aktif',  'ipk'=>3.88,'alamat'=>'Jl. Cawang No. 10'],
            ['nim'=>'20110011','nama'=>'Kevin Ramadhan',     'email'=>'kevin.r@student.ac.id',   'no_hp'=>'081123456711','prodi'=>'Teknik Informatika',   'fakultas'=>'Ilmu Komputer','angkatan'=>2020,'status'=>'lulus',  'ipk'=>3.95,'alamat'=>'Jl. Tebet No. 11'],
            ['nim'=>'20110012','nama'=>'Lina Marlina',       'email'=>'lina.m@student.ac.id',    'no_hp'=>'082234567812','prodi'=>'Sistem Informasi',     'fakultas'=>'Ilmu Komputer','angkatan'=>2020,'status'=>'lulus',  'ipk'=>3.70,'alamat'=>'Jl. Pasar Minggu No. 12'],
            ['nim'=>'23110013','nama'=>'Muhammad Rizki',     'email'=>'mrizki@student.ac.id',    'no_hp'=>'083345678913','prodi'=>'Teknik Informatika',   'fakultas'=>'Ilmu Komputer','angkatan'=>2023,'status'=>'aktif',  'ipk'=>3.10,'alamat'=>'Jl. Mampang No. 13'],
            ['nim'=>'22110014','nama'=>'Nadia Permata Sari', 'email'=>'nadia.p@student.ac.id',   'no_hp'=>'084456789014','prodi'=>'Manajemen',            'fakultas'=>'Ekonomi',      'angkatan'=>2022,'status'=>'aktif',  'ipk'=>3.55,'alamat'=>'Jl. Kebayoran No. 14'],
            ['nim'=>'21110015','nama'=>'Oscar Pratama',      'email'=>'oscar.p@student.ac.id',   'no_hp'=>'085567890115','prodi'=>'Teknik Elektro',       'fakultas'=>'Teknik',       'angkatan'=>2021,'status'=>'keluar','ipk'=>2.30,'alamat'=>'Jl. Cilandak No. 15'],
            ['nim'=>'23110016','nama'=>'Putri Andriani',     'email'=>'putri.a@student.ac.id',   'no_hp'=>'086678901216','prodi'=>'Akuntansi',            'fakultas'=>'Ekonomi',      'angkatan'=>2023,'status'=>'aktif',  'ipk'=>3.80,'alamat'=>'Jl. Lebak Bulus No. 16'],
            ['nim'=>'22110017','nama'=>'Qodri Maulana',      'email'=>'qodri.m@student.ac.id',   'no_hp'=>'087789012317','prodi'=>'Teknik Informatika',   'fakultas'=>'Ilmu Komputer','angkatan'=>2022,'status'=>'aktif',  'ipk'=>3.35,'alamat'=>'Jl. Ciputat No. 17'],
            ['nim'=>'20110018','nama'=>'Rina Septiani',      'email'=>'rina.s@student.ac.id',    'no_hp'=>'088890123418','prodi'=>'Sistem Informasi',     'fakultas'=>'Ilmu Komputer','angkatan'=>2020,'status'=>'lulus',  'ipk'=>3.60,'alamat'=>'Jl. Serpong No. 18'],
            ['nim'=>'21110019','nama'=>'Surya Purnama',      'email'=>'surya.p@student.ac.id',   'no_hp'=>'089901234519','prodi'=>'Manajemen',            'fakultas'=>'Ekonomi',      'angkatan'=>2021,'status'=>'cuti',   'ipk'=>2.90,'alamat'=>'Jl. BSD No. 19'],
            ['nim'=>'23110020','nama'=>'Tasya Amelia',       'email'=>'tasya.a@student.ac.id',   'no_hp'=>'081012345620','prodi'=>'Teknik Informatika',   'fakultas'=>'Ilmu Komputer','angkatan'=>2023,'status'=>'aktif',  'ipk'=>3.72,'alamat'=>'Jl. Tangerang No. 20'],
        ];

        foreach ($mahasiswas as $data) {
            Mahasiswa::create(array_merge($data, [
                'tanggal_lahir' => now()->subYears(rand(19, 25))->subDays(rand(0, 365))->format('Y-m-d'),
            ]));
        }
    }
}
