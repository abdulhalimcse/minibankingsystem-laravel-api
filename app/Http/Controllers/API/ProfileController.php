<?php

namespace App\Http\Controllers\API;

//use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Profile;

class ProfileController extends BaseController
{
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'national_id' => [
				'required',
				function ($attribute, $value, $fail) {
					if (strlen($value) != 10 && strlen($value) != 13 && strlen($value) != 17) {
						$fail($attribute.' must be 10 or 13 or 17 digits.');
					}
				},
			],
            'contact_no' => [
				'nullable',
				'digits:11',
				function ($attribute, $value, $fail) {
					if ($value[0] != 0 ||  $value[1] != 1) {
						$fail($attribute.' is invalid.');
					}
				},
			],
            'country_id' => 'required',
            'city_id' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
		
		if(isset($input['national_id']) && Profile::isProfileNationalIdExists($input['national_id'])) {
			return $this->sendError('National ID is exists.', $validator->errors());     
		}
		
		if(isset($input['contact_no']) && Profile::isProfileContactNoExists($input['contact_no'])) {
			return $this->sendError('Contact No is exists.', $validator->errors());     
		}
		
		DB::transaction(function () use ($input) {
			
			$user = Auth::user(); 
			
			$isProfile = Profile::isProfileExists($user->id);
			
			if(!$isProfile) {
				DB::table('profiles')->insert(
					[
						'user_id' => $user->id,
						'img' => isset($input['img']) ? $input['img'] : '', 
						'national_id' => isset($input['national_id']) ? $input['national_id'] : '', 
						'contact_no' => isset($input['contact_no']) ? $input['contact_no'] : '', 
						'country_id' => $input['country_id'], 
						'city_id' => $input['city_id'], 
						'present_address' => $input['present_address'], 
						'permanent_address' => $input['permanent_address'], 
						'created_by' => $user->id,
						'status' => 3
					]
				);
			} else {
				DB::table('profiles')->where('user_id', $user->id)->update(
					[
						'user_id' => $user->id,
						'img' => isset($input['img']) ? $input['img'] : '', 
						'national_id' => isset($input['national_id']) ? $input['national_id'] : '', 
						'contact_no' => isset($input['contact_no']) ? $input['contact_no'] : '', 
						'country_id' => $input['country_id'], 
						'city_id' => $input['city_id'], 
						'present_address' => $input['present_address'], 
						'permanent_address' => $input['permanent_address'], 
						'created_by' => $user->id,
						'status' => 3
					]
				);
				
			}
			
			DB::table('accounts')->where('user_id', $user->id)->where('status', 0)->update(['status' => 3]);
			
		}, 5);
		
		$success['msg'] = 'Updated';
   
        return $this->sendResponse($success, 'Profile updated successfully.');
    }

}
