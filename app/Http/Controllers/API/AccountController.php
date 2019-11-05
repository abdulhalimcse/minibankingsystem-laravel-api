<?php

namespace App\Http\Controllers\API;

//use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Account;
use Validator;
use App\Http\Resources\Account as AccountResource;

class AccountController extends BaseController
{
    public function getBalance(Request $request) 
	{
		$input = $request->all();
		$validator = Validator::make($input, [
            'account_no' => 'required'
        ]);
		
		if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		
		if(isset($input['account_no']) && !empty($input['account_no']) && !Account::isAccountActive($input['account_no'])) {
			return $this->sendError('Not Active or Exists.', ['error'=>'Your Account is not active or exists. Please active your account.']);
		}
		
		$success['current_balance'] = Account::getCurrentBalance($input['account_no']);
		$success['success'] = true;
        return $this->sendResponse($success, 'Balance is retrieved successfully.');
		
	}
}
