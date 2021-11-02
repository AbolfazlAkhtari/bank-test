<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Transaction\CreateTransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    use ApiResponse;

    /**
     * @param CreateTransactionRequest $request
     * @return Response
     */
    public function create(CreateTransactionRequest $request): Response
    {
        $validatedDate = $request->validationData();

        $sender = Account::query()->find($validatedDate['sender_account_id']);

        if ($sender->amount < $validatedDate['amount']) {
            return $this->response(null, __('messages.sender_amount_is_not_enough'), 422);
        }

        $receiver = Account::query()->find($validatedDate['receiver_account_id']);

        $sender->decrement('amount', $validatedDate['amount']);
        $receiver->increment('amount', $validatedDate['amount']);
        $transaction = Transaction::query()->create($validatedDate);

        $transaction->load(['sender.user', 'receiver.user']);

        return $this->response($transaction,  __('messages.transaction_created'), 201);
    }

    /**
     * @param Account $account
     * @return Response
     */
    public function history(Account $account): Response
    {
        return $this->response($account->transactions());
    }
}
