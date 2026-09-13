<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 日本語（ja_JP）の設定でFakerを初期化します
        $faker = Faker::create('ja_JP');

        // ランダムに選ぶためのカテゴリとタグをデータベースからすべて取得します
        $categories = Category::all();
        $tags = Tag::all();

        // 要件通り、20件のダミーデータをループで作成します
        for ($i = 0; $i < 20; $i++) {
            // お問い合わせデータを1件作成
            $contact = Contact::create([
                'category_id' => $categories->random()->id, // 既存のカテゴリからランダム
                'first_name'  => $faker->firstName,
                'last_name'   => $faker->lastName,
                'gender'      => $faker->numberBetween(1, 3), // 1:男性, 2:女性, 3:その他
                'email'       => $faker->safeEmail,
                'tel'         => substr($faker->phoneNumber, 0, 11), // ハイフンなし等に対応できるよう調整
                'address'     => $faker->prefecture . $faker->city . $faker->streetAddress,
                'building'    => $faker->optional(0.7)->secondaryAddress, // 70%の確率で建物名を入力（nullable対応）
                'detail'      => $faker->realText(100), // 120文字以内のテキスト
            ]);

            // 要件通り、既存のタグからランダムに1〜3件を選んで attach() で紐付けます
            $randomTags = $tags->random(rand(1, 3));
            $contact->tags()->attach($randomTags);
        }
    }
}