<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Transaction;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    /**
     * A transaction can be created for transferring money between two accounts
     *
     * @return void
     */
    public function test_transaction_can_be_created()
    {
        $initialAmount = rand(1000, 100000) / 100;
        $transferAmount = rand(100, 10000) / 100;
        $firstAcc = Account::factory(['amount' => $initialAmount])->create();
        $secondAcc = Account::factory(['amount' => $initialAmount])->create();

        $data = [
            'sender_account_id' => $firstAcc->id,
            'receiver_account_id' => $secondAcc->id,
            'amount' => $transferAmount,
        ];

        $response = $this->post('/api/transactions/create', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('transactions', $data);
        $this->assertEquals($initialAmount - $transferAmount, Account::query()->find($firstAcc->id)->amount);
        $this->assertEquals($initialAmount + $transferAmount, Account::query()->find($secondAcc->id)->amount);
    }

    /**
     * Transaction can not be made with invalid data
     *
     * @return void
     */
    public function test_transaction_can_not_be_created_when_data_is_invalid()
    {
        // amount should be valid number and the users should exist in database
        $response = $this->post('/api/transactions/create', [
            'sender_account_id' => 100, // this user id does not exist in users table
            'receiver_account_id' => 200, // this user id does not exist in users table
            'amount' => 'amount', // invalid amount value,
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertJsonValidationErrors([
            'sender_account_id',
            'receiver_account_id',
            'amount',
        ]);

        $initialAmount = rand(1000, 100000) / 100;
        $transferAmount = rand(100, 10000) / 100;
        $firstAcc = Account::factory(['amount' => $initialAmount])->create();
        $secondAcc = Account::factory(['amount' => $initialAmount])->create();

        // amount should be a number bigger than 0
        $response = $this->post('/api/transactions/create', [
            'sender_account_id' => $firstAcc->id, // this user id does not exist in users table
            'receiver_account_id' => $secondAcc->id, // this user id does not exist in users table
            'amount' => -1, // invalid amount value,
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertJsonValidationErrors([
            'amount',
        ]);

        // sender and receiver can not be the same
        $response = $this->post('/api/transactions/create', [
            'sender_account_id' => $firstAcc->id, // this user id does not exist in users table
            'receiver_account_id' => $firstAcc->id, // this user id does not exist in users table
            'amount' => $transferAmount, // invalid amount value,
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertJsonValidationErrors([
            'receiver_account_id',
        ]);
    }

    /**
     * Transaction can not be made when amount is bigger than sender deposit
     *
     * @return void
     */
    public function test_transaction_can_not_be_created_when_sender_amount_is_not_enough()
    {
        $initialAmount = rand(100, 10000) / 100;
        $transferAmount = rand(1000, 100000) / 100;
        $firstAcc = Account::factory(['amount' => $initialAmount])->create();
        $secondAcc = Account::factory(['amount' => $initialAmount])->create();

        $response = $this->post('/api/transactions/create', [
            'sender_account_id' => $firstAcc->id,
            'receiver_account_id' => $secondAcc->id,
            'amount' => $transferAmount,
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(422);
    }

    /**
     * /api/transactions/history/{accountId} returns the history of {accountId}
     *
     * @return void
     */
    public function test_history_api_can_be_used_to_retrieve_a_given_account_history()
    {
        $initialAmount = rand(1000, 100000) / 100;
        $transferAmount = rand(10, 1000) / 10;
        $firstAcc = Account::factory(['amount' => $initialAmount])->create();
        $secondAcc = Account::factory(['amount' => $initialAmount])->create();

        Transaction::factory([
            'sender_account_id' => $firstAcc->id,
            'receiver_account_id' => $secondAcc->id,
            'amount' => $transferAmount
        ])->count(3)->create();

        Transaction::factory([
            'sender_account_id' => $secondAcc->id,
            'receiver_account_id' => $firstAcc->id,
            'amount' => $transferAmount
        ])->count(2)->create();

        $response = $this->get('/api/transactions/history/' . $firstAcc->id);
        $response->assertStatus(200);
        $this->assertEquals($firstAcc->transactions()->toArray(), $response->json()['data']);
    }
}
