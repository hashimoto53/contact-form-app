<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        // 1:男性, 2:女性, 3:その他 を定義通りに生成
        $gender = $this->faker->numberBetween(1, 3);

        // 性別と完全に連動する日本語のファーストネーム配列
        $maleNames = ['亮介', '太郎', '大輔', '健太', '翔太', '拓海', '和也', '翼', '京助', '真樹'];
        $femaleNames = ['桃子', '裕美子', 'さゆり', 'あすか', '陽子', '結衣', '美咲', '千尋', '彩香', '和美'];
        
        if ($gender === 1) {
            $firstName = $this->faker->randomElement($maleNames);
        } elseif ($gender === 2) {
            $firstName = $this->faker->randomElement($femaleNames);
        } else {
            // その他（3）の場合は男女どちらからでもランダムに選択
            $firstName = $this->faker->randomElement(array_merge($maleNames, $femaleNames));
        }

        return [
            'category_id' => $this->faker->numberBetween(1, 5),
            'first_name'  => $firstName,
            'last_name'   => $this->faker->lastName(), // 苗字は共通でランダム
            'gender'      => $gender,
            'email'       => $this->faker->safeEmail(),
            'tel'         => $this->faker->numerify('090########'), 
            'address'     => $this->faker->address(),
            'building'    => $this->faker->secondaryAddress(),
            'detail'      => $this->faker->realText(50),
        ];
    }
}