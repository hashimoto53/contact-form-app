<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

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

        // 性別と完全に連動する日本語のファーストネーム配列を用意します
        $maleNames = ['亮介', '太郎', '大輔', '健太', '翔太', '拓海', '和也', '翼', '京助', '真樹'];
        $femaleNames = ['桃子', '裕美子', 'さゆり', 'あすか', '陽子', '結衣', '美咲', '千尋', '彩香', '和美'];

        // 要件通り、20件のダミーデータをループで作成します
        for ($i = 0; $i < 20; $i++) {
            // 先に性別（1:男性, 2:女性, 3:その他）をランダムに決定
            $gender = $faker->numberBetween(1, 3);

            // 性別に応じて名前を割り当てる
            if ($gender === 1) {
                $firstName = $faker->randomElement($maleNames);
            } elseif ($gender === 2) {
                $firstName = $faker->randomElement($femaleNames);
            } else {
                // その他の場合は男女どちらからでもランダムに選択
                $firstName = $faker->randomElement(array_merge($maleNames, $femaleNames));
            }

            // お問い合わせデータを1件作成
            $contact = Contact::create([
                'category_id' => $categories->random()->id, // 既存のカテゴリからランダム
                'first_name' => $firstName,
                'last_name' => $faker->lastName,
                'gender' => $gender,
                'email' => $faker->safeEmail,
                'tel' => $faker->numerify('090########'), // ハイフンなしの11桁の数字に固定
                'address' => $faker->prefecture.$faker->city.$faker->streetAddress,
                'building' => $faker->optional(0.7)->secondaryAddress, // 70%の確率で建物名を入力
                'detail' => $faker->realText(100), // 120文字以内のテキスト
            ]);

            // 要件通り、既存のタグからランダムに1〜3件を選んで attach() で紐付けます
            $randomTags = $tags->random(rand(1, 3));
            $contact->tags()->attach($randomTags);
        }
    }
}
