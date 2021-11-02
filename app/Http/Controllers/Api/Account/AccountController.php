<?php

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Account\CreateAccountRequest;
use App\Models\Account;
use App\Traits\ApiResponse;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    use ApiResponse;

    /**
     * @param CreateAccountRequest $request
     * @return Response
     */
    public function create(CreateAccountRequest $request): Response
    {
        $validatedDate = $request->validationData();

        $account = Account::query()->create($validatedDate);

        return $this->response($account,  __('messages.account_created'), 201);
    }

    /**
     * @param Account $account
     * @return Response
     */
    public function show(Account $account): Response
    {
        $account->load('user');
        return $this->response($account);
    }
}
