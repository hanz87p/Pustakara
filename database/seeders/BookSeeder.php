<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $fiksi    = Category::where('slug', 'fiksi')->first()->id;
        $pelajaran= Category::where('slug', 'pelajaran')->first()->id;
        $komik    = Category::where('slug', 'komik')->first()->id;
        $novel    = Category::where('slug', 'novel')->first()->id;
        $nonfiksi = Category::where('slug', 'non-fiksi')->first()->id;

        $buku = [
            ['BKU-001', 'Laskar Pelangi',              'Andrea Hirata',    'Bentang Pustaka',  2005, 5, $novel,    'A-01'],
            ['BKU-002', 'Bumi Manusia',                'Pramoedya A.T.',   'Hasta Mitra',      1980, 3, $novel,    'A-02'],
            ['BKU-003', 'Pemrograman PHP untuk Pemula','Abdul Kadir',     'Andi Publisher',   2020, 4, $pelajaran,'B-01'],
            ['BKU-004', 'Laravel Framework Lanjutan',  'Rizky Pratama',    'Elex Media',       2022, 2, $pelajaran,'B-02'],
            ['BKU-005', 'Naruto Vol. 1',               'Masashi Kishimoto','Elex Media',       2003, 6, $komik,    'C-01'],
            ['BKU-006', 'One Piece Vol. 1',            'Eiichiro Oda',     'Elex Media',       2000, 4, $komik,    'C-02'],
            ['BKU-007', 'Sapiens',                     'Yuval N. Harari',  'Kepustakaan Populer',2017, 3,$nonfiksi,'D-01'],
            ['BKU-008', 'Filosofi Teras',              'Henry Manampiring','Kompas',           2018, 5, $nonfiksi, 'D-02'],
            ['BKU-009', 'Harry Potter dan Batu Bertuah','J.K. Rowling',    'Gramedia',         1997, 4, $fiksi,    'E-01'],
            ['BKU-010', 'Matematika Kelas XII',        'Tim Kemendikbud',  'Kemendikbud',      2020, 10,$pelajaran,'B-03'],
        ];

        foreach ($buku as $b) {
            Book::create([
                'nomor_induk' => $b[0],
                'judul'       => $b[1],
                'penulis'     => $b[2],
                'penerbit'    => $b[3],
                'tahun_terbit'=> $b[4],
                'stok'        => $b[5],
                'stok_total'  => $b[5],
                'category_id' => $b[6],
                'rak'         => $b[7],
            ]);
        }
    }
}