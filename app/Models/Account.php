<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Transaction::class, 'receiver_account_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_account_id');
    }

    public function transactions()
    {
        return $this->incomes()->with(['sender.user', 'receiver.user'])->get()
            ->merge($this->payments()->with(['sender.user', 'receiver.user'])->get());
    }
}
