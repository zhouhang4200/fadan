<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SteamStorePrice extends Model
{
	//黑名单为空
	protected $guarded = [];

	public static function checkUserId($id){
		$user = self::where('user_id',$id)->first();
		if($user){
			return true;
		}
		return false;
	}
}
