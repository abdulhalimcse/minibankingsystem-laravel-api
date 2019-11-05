<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Account;
use App\Transaction;
use App\Http\Resources\Account as AccountResource;

class TransactionController extends BaseController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'from_account_no' => 'required',
            'deposit' => 'required|numeric|min:1'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		
		if(isset($input['from_account_no']) && !empty($input['from_account_no']) && !Account::isAccountActive($input['from_account_no'])) {
			return $this->sendError('Not Active.', ['error'=>'Your Account is not active. Please active your account.']);
		}
   
       
		DB::transaction(function () use ($input) {
			
			$currentBalanceBeforeSave = Account::getCurrentBalance($input['from_account_no']);
			$currentBalance = ($input['deposit'] + $currentBalanceBeforeSave);
			$user = Auth::user(); 
			
			DB::table('transactions')->insert(
				[
					'user_id' => $user->id,
					'from_account_no' => $input['from_account_no'], 
					'balance' => $currentBalanceBeforeSave, 
					'deposit' => $input['deposit'],
					'current_balance' => $currentBalance,
					'created_by' => $user->id, 
					'status' => 3 
				]
			);
			
			DB::table('accounts')->where('account_no', $input['from_account_no'])->where('status', 3)->update(['current_balance' => $currentBalance]);
			
		}, 5);
		
		$success['success'] = true;
        return $this->sendResponse($success, 'Deposit is done successfully.');
    }

	public function transfer(Request $request)
    {
		$input = $request->all();

        $validator = Validator::make($input, [
            'from_account_no' => 'required',
            'to_account_no' => 'required',
            'withdraw' => 'required|numeric|min:1'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		
		if(isset($input['from_account_no']) && !empty($input['from_account_no']) && !Account::isAccountActive($input['from_account_no'])) {
			return $this->sendError('Not Active or Exists.', ['error'=>'Your Account is not active or exists. Please active your account.']);
		}
		
		if(isset($input['to_account_no']) && !empty($input['to_account_no']) && !Account::isAccountActive($input['to_account_no'])) {
			return $this->sendError('Not Active or Exists.', ['error'=>'Your Transfered Account is not active or exists. Please active your account.']);
		}
		
		$currentBalanceBeforeSaveFromAccount = Account::getCurrentBalance($input['from_account_no']);
		
		if(isset($currentBalanceBeforeSaveFromAccount) && isset($input['withdraw']) && $currentBalanceBeforeSaveFromAccount < $input['withdraw']) {
			return $this->sendError('Not Sufficient.', ['error'=>'Your Account balance is insufficient.']);
		}
   
       
		DB::transaction(function () use ($input, $currentBalanceBeforeSaveFromAccount) {
			
			if(isset($input['to_account_no']) && !empty($input['to_account_no'])) {
				$currentBalanceBeforeSave = Account::getCurrentBalance($input['to_account_no']);
				$currentBalance = ($currentBalanceBeforeSave + $input['withdraw']);
				$user = Auth::user(); 
				
				DB::table('transactions')->insert(
					[
						'user_id' => $user->id,
						'from_account_no' => $input['from_account_no'], 
						'to_account_no' => $input['to_account_no'], 
						'balance' => $currentBalanceBeforeSave, 
						'deposit' => $input['withdraw'],  // DEPOSIT FOR TO ACCOUNT 
						'current_balance' => $currentBalance,
						'created_by' => $user->id,
						'status' => 3 	
					]
				);
				
				DB::table('accounts')->where('account_no', $input['to_account_no'])->where('status', 3)->update(['current_balance' => $currentBalance]);
			}
			
			if(isset($input['from_account_no']) && !empty($input['from_account_no'])) {
				$currentBalanceBeforeSave = $currentBalanceBeforeSaveFromAccount;
				$currentBalance = ($currentBalanceBeforeSave - $input['withdraw']);
				$user = Auth::user(); 
				
				DB::table('transactions')->insert(
					[
						'user_id' => $user->id,
						'from_account_no' => $input['from_account_no'], 
						'to_account_no' => $input['to_account_no'], 
						'balance' => $currentBalanceBeforeSave, 
						'withdraw' => $input['withdraw'], // WITHDRAW = IT WILL BE WITHDRAW FOR TRANSFER 
						'current_balance' => $currentBalance,
						'created_by' => $user->id,
						'status' => 3 	
					]
				);
				
				DB::table('accounts')->where('account_no', $input['from_account_no'])->where('status', 3)->update(['current_balance' => $currentBalance]);
			}
			
		}, 5);
		
		$success['success'] = true;
        return $this->sendResponse($success, 'Transfer is done successfully.');
	}
   
}
