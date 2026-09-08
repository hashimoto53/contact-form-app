<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Category;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // カテゴリの初期データを作成
        Category::factory()->count(5)->create();

        // お問合せのテストデータを35件作成
        Contact::factory()->count(35)->create();
    }
}
