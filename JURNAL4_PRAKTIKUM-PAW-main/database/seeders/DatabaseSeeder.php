<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
        // 6. Dua Mata Kuliah Tambahan (semester berbeda)
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF302'],
            [
                'nama_mk' => 'Basis Data Lanjut',
                'deskripsi' => 'Membahas normalisasi, query kompleks, indexing, transaksi, dan optimasi basis data relasional.',
                'sks' => 3,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF201'],
            [
                'nama_mk' => 'Pemrograman Berorientasi Objek',
                'deskripsi' => 'Konsep OOP: class, inheritance, polymorphism, encapsulation, dan penerapannya dalam PHP.',
                'sks' => 3,
                'semester' => 'Ganjil 2025/2026',
                'dosen_id' => $dosen->id,
            ]
        );

        // 7. Mahasiswa Tambahan
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'mahasiswa2@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // 8. Pendaftaran Mahasiswa ke Mata Kuliah
        $course1->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);
        $course2->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
            $mahasiswa2->id => ['status' => 'pending'],
        ]);
        $course3->students()->syncWithoutDetaching([
            $mahasiswa2->id => ['status' => 'approved'],
        ]);

        // 9. Dua Tugas
        $assignment1 = Assignment::updateOrCreate(
            ['course_id' => $course1->id, 'judul' => 'Tugas 1: Membuat CRUD Sederhana dengan Laravel'],
            [
                'deskripsi' => 'Buat aplikasi CRUD data buku menggunakan Eloquent ORM dan Blade Template.',
                'deadline' => now()->addDays(7),
            ]
        );

        $assignment2 = Assignment::updateOrCreate(
            ['course_id' => $course2->id, 'judul' => 'Tugas 1: Normalisasi Basis Data'],
            [
                'deskripsi' => 'Lakukan normalisasi 1NF sampai 3NF pada studi kasus sistem perpustakaan.',
                'deadline' => now()->addDays(14),
            ]
        );

        // 10. Contoh Submission
        // Submission yang sudah dinilai
        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'student_id' => $mahasiswa1->id],
            [
                'jawaban' => 'CRUD buku sudah selesai dibuat. Source code ada di repository GitHub terlampir.',
                'nilai' => 88,
                'catatan_dosen' => 'Bagus, struktur controller sudah rapi. Tambahkan validasi pada form input.',
            ]
        );

        // Submission yang belum dinilai (nilai & catatan_dosen null)
        Submission::updateOrCreate(
            ['assignment_id' => $assignment1->id, 'student_id' => $mahasiswa2->id],
            [
                'jawaban' => 'Aplikasi CRUD sudah berjalan, namun fitur pencarian masih dalam pengerjaan.',
            ]
        );

        Submission::updateOrCreate(
            ['assignment_id' => $assignment2->id, 'student_id' => $mahasiswa1->id],
            [
                'jawaban' => 'Hasil normalisasi terlampir: tabel anggota, buku, dan peminjaman sudah memenuhi 3NF.',
            ]
        );
    }
}
