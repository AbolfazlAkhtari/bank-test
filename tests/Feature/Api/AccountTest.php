<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An account can be created with a valid user_id and valid amount
     *
     * @return void
     */
    public function test_account_can_be_created_for_a_user_with_initial_amount()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'amount' => rand(1000, 100000) / 100,
        ];

        $response = $this->post('/api/accounts/create', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('accounts', $data);
    }

    /*
     * Create Account Api returns validation error in case of invalid user_id or invalid amount
     *
     * @return void
     */
    public function test_account_can_not_be_created_with_invalid_data()
    {
        $response = $this->post('/api/accounts/create', [
            'user_id' => 100, // this user id does not exist in users table
            'amount' => 'amount' // invalid amount value,
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertJsonValidationErrors([
            'user_id',
            'amount'
        ]);


        $response = $this->post('/api/accounts/create', [
            'user_id' => 100, // this user id does not exist in users table
            'amount' => -12 // invalid amount value,
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertJsonValidationErrors([
            'user_id',
            'amount'
        ]);
    }

    /*
     * A single account can be accessed via /api/accounts/{accountId} api
     *
     * @return void
     */
    public function test_a_single_account_can_be_accessed()
    {
        $account = Account::factory()->create();
        $response = $this->get('/api/accounts/' . $account->id);
        $response->assertStatus(200);
    }
}
