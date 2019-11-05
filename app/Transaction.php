<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'from_account_no', 'to_account_no', 'balance', 'deposit', 'withdraw', 'current_balance', 'status', 'created_by', 'modified_by'
    ];
}
