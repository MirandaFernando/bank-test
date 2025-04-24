<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Transaction extends Model
{
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_TRANSFER = 'transfer';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REVERSED = 'reversed';
    const STATUS_PENDING = 'pending';

    protected $fillable = [
        'amount', 'type', 'status', 'sender_id', 'receiver_id', 'reversed_at', 'wallet_id'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
