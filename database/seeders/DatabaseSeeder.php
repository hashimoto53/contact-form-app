<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // すべての個別シーダーを要件に定められた正しい順番で呼び出します
        $this->call([
            UserSeeder::class,     // 1. 初期管理者（1件）
            CategorySeeder::class, // 2. 固定カテゴリ（日本語5件）
            TagSeeder::class,      // 3. 固定タグ（日本語5件）
            ContactSeeder::class,  // 4. ダミーお問い合わせ（日本語20件・タグ紐付け）
        ]);
    }
}