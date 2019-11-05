<?php

namespace App\Http\Controllers\API;

//use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\User;
use App\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Http\Resources\Account as AccountResource;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users|max:50',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
	
 
		try {

			DB::beginTransaction();
			
			$input = $request->all(); 
			$input['password'] = bcrypt($input['password']);
			$user = User::create($input);
			//dd(Account::getLastAccountNo());
			$accountNo = Account::getLastAccountNo() + 1;
			$account = Account::create(['user_id' => $user->id, 'account_no' => $accountNo, 'account_name' => $user->name, 'created_by']);
			//$success['token'] =  $user->createToken('MyApp')->accessToken;
			$success['name'] =  $user->name;
			$success['account_no'] =  isset($account->account_no) ? $account->account_no : '';
			$success['msg'] =  'Your registeration is done successfully! Your account no is ' . $success['account_no'] . '. Please active the account update your profile.'; 
			 
			DB::commit();
			
		} catch (\PDOException $e) {
				
			$error['msg'] =  'Your registeration is failed. Please try again. Error : ' . $e->getMessage(); 
				
			DB::rollBack();
		}

		
		if(!empty($success['msg'])) {
			return $this->sendResponse($success, $success['msg']);
		}
		
		return $this->sendError($error['msg'], ['error'=>'Failed']);
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
			$accountArr = Account::getSelectedAccount($user->id);
			
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            $success['name'] =  $user->name;
            $success['selected_account_no'] = $accountArr['account_no'];
			if(isset($accountArr['status']) && $accountArr['status'] != 3) { 
				$success['msg'] = 'To active your account. Please update your profile';
			}
   
            return $this->sendResponse($success, 'User login successfully.');
        } else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
