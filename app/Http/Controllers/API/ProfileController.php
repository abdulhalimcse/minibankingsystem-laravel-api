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
            'national_id' => 'required|digits:10',
            'contact_no' => 'nullable|digits:11',
            'country_id' => 'required',
            'city_id' => 'required',
            'present_address' => 'required',
            'permanent_address' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
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
