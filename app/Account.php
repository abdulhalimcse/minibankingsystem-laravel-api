<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
     protected $fillable = [
        'user_id', 'account_no', 'account_name', 'current_balance', 'account_type'
    ];
	
	public function user()
	{
		return $this->belongsTo('App\User');
	}
	
	protected function getLastAccountNo()
	{
		$account = Account::select('account_no')->orderBy('id', 'desc')->first();
		//dd($account);
		$lastAccountNo = 1010000000000;
		if(isset($account->account_no) && $account->account_no > 0) {
			$lastAccountNo = $account->account_no;
		}
		
		return $lastAccountNo;
	}
	
	protected function getSelectedAccount($userId)
	{
		$account = Account::select('account_no', 'status')->where('user_id', $userId)->orderBy('id', 'desc')->first();
		$rtrArr = [];
		if(isset($account->account_no) && $account->account_no > 0) {
			$rtrArr['account_no'] = $account->account_no;
			$rtrArr['status'] = $account->status;
		}
		
		return $rtrArr;
	}
	
	protected function getCurrentBalance($accountNo)
	{
		$account = Account::select('current_balance')->where('account_no', $accountNo)->where('status', 3)->first();
		$currentBalance  = 0;
		if(isset($account->current_balance) && $account->current_balance > 0) {
			$currentBalance = $account->current_balance;
		}
		
		return $currentBalance;
	}
	
	protected function isAccountActive($accountNo)
	{
		$account = Account::select('status')->where('account_no', $accountNo)->first();
		if(isset($account->status) && $account->status == 3) {
			return true;
		}
		return false;
	}
}
