<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Fiksi',
            'Non-Fiksi',
            'Pelajaran',
            'Komik',
            'Novel',
        ];

        foreach ($kategori as $nama) {
            Category::create([
                'nama_kategori' => $nama,
                'slug' => Str::slug($nama),
            ]);
        }
    }
}