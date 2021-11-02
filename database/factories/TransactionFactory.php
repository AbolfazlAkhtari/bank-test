<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_account_id' => Account::factory()->create()->id,
            'receiver_account_id' => Account::factory()->create()->id,
            'amount' => $this->faker->randomFloat()
        ];
    }
}
