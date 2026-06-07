<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        // ── Buat user admin ──
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@mail.com',
                'password' => Hash::make('password'),
            ]
        );

        // ── Data dummy mahasiswa ──
        $data = [
            ['nim'=>'2312310001','nama'=>'Andi Saputra',     'jurusan'=>'Informatika',      'angkatan'=>'2023','email'=>'andi@unpam.ac.id',    'hp'=>'081234567890','status'=>'Aktif'],
            ['nim'=>'2212310045','nama'=>'Budi Santoso',     'jurusan'=>'Sistem Informasi', 'angkatan'=>'2022','email'=>'budi@unpam.ac.id',    'hp'=>'082345678901','status'=>'Aktif'],
            ['nim'=>'2112310078','nama'=>'Citra Dewi',       'jurusan'=>'Manajemen',        'angkatan'=>'2021','email'=>'citra@unpam.ac.id',   'hp'=>'083456789012','status'=>'Lulus'],
            ['nim'=>'2312310029','nama'=>'Dian Pratama',     'jurusan'=>'Teknik Elektro',   'angkatan'=>'2023','email'=>'dian@unpam.ac.id',    'hp'=>'084567890123','status'=>'Aktif'],
            ['nim'=>'2212310056','nama'=>'Eka Rahayu',       'jurusan'=>'Akuntansi',        'angkatan'=>'2022','email'=>'eka@unpam.ac.id',     'hp'=>'085678901234','status'=>'Cuti'],
            ['nim'=>'2312310033','nama'=>'Fajar Kurniawan',  'jurusan'=>'Informatika',      'angkatan'=>'2023','email'=>'fajar@unpam.ac.id',   'hp'=>'086789012345','status'=>'Aktif'],
            ['nim'=>'2112310090','nama'=>'Galuh Permata',    'jurusan'=>'Hukum',            'angkatan'=>'2021','email'=>'galuh@unpam.ac.id',   'hp'=>'087890123456','status'=>'Lulus'],
            ['nim'=>'2012310011','nama'=>'Hendra Wijaya',    'jurusan'=>'Sistem Informasi', 'angkatan'=>'2020','email'=>'hendra@unpam.ac.id',  'hp'=>'088901234567','status'=>'Lulus'],
            ['nim'=>'2312310044','nama'=>'Indah Lestari',    'jurusan'=>'Manajemen',        'angkatan'=>'2023','email'=>'indah@unpam.ac.id',   'hp'=>'089012345678','status'=>'Aktif'],
            ['nim'=>'2212310067','nama'=>'Joko Purnomo',     'jurusan'=>'Informatika',      'angkatan'=>'2022','email'=>'joko@unpam.ac.id',    'hp'=>'081123456789','status'=>'Aktif'],
            ['nim'=>'2112310055','nama'=>'Kartika Sari',     'jurusan'=>'Akuntansi',        'angkatan'=>'2021','email'=>'kartika@unpam.ac.id', 'hp'=>'082234567890','status'=>'Lulus'],
            ['nim'=>'2312310088','nama'=>'Lukman Hakim',     'jurusan'=>'Teknik Elektro',   'angkatan'=>'2023','email'=>'lukman@unpam.ac.id',  'hp'=>'083345678901','status'=>'Aktif'],
        ];

        foreach ($data as $row) {
            Mahasiswa::updateOrCreate(['nim' => $row['nim']], $row);
        }
    }
}
