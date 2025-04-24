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

    public function getUserId()
    {
        // Assuming the user ID is stored in the sender_id field
        return $this->sender_id;
    }

    public function setUserId($value)
    {
        $this->sender_id = $value;
    }

    public function getReceiverId()
    {
        return $this->receiver_id;
    }

    public function setReceiverId($value)
    {
        $this->receiver_id = $value;
    }

    public function setReversedAt($value)
    {
        $this->reversed_at = $value;
    }

    public function getReversedAt()
    {
        return $this->reversed_at;
    }

    public function getWalletId()
    {
        return $this->wallet_id;
    }

    public function setWalletId($value)
    {
        $this->wallet_id = $value;
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }


    public function getAmount()
    {
        return $this->amount;
    }


    public function setAmount($value)
    {
        $this->amount = $value;
    }

    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($value)
    {
        $this->status = $value;
    }
}
