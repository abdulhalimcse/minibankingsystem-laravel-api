<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'img', 'national_id', 'contact_no', 'country_id', 'city_id', 'present_address', 'permanent_address'
    ];
	
	protected function isProfileExists($userId)
	{
		$profile = Profile::select('id')->where('user_id', $userId)->first();

		if(isset($profile->id) && $profile->id > 0) {
			return true;
		}
		
		return false;
	}
}
