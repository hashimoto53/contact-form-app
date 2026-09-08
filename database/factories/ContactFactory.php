<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'category_id' => $this->faker->numberBetween(1, 5),
            'first_name'  => $this->faker->firstName(),
            'last_name'   => $this->faker->lastName(),
            'gender'      => $this->faker->numberBetween(1, 3),
            'email'       => $this->faker->safeEmail(),
            'tel'         => $this->faker->numerify('000########'), // ← 数字のみの11桁（例: 00012345678）に変更
            'address'     => $this->faker->address(),
            'building'    => $this->faker->secondaryAddress(),
            'detail'      => $this->faker->realText(50),
        ];
    }
}