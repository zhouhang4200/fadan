<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAutoAssign extends Model
{

	//黑名单为空
	protected $guarded = [];

	public function game()
	{
		return $this->hasOne(Game::Class, 'id', 'game_id');
	}

	/**
	 * 验证游戏
	 */
	public static function checkGame($gameId,$creator_primary_user_id,$gainer_primary_user_id)
	{
		$oriented = self::where(['game_id' => $gameId,'creator_primary_user_id'=>$creator_primary_user_id,'gainer_primary_user_id'=>$gainer_primary_user_id])->first();
		if ($oriented) {
			return true;
		}
		return false;
	}

	/**
	 * 验证游戏
	 */
	public static function creatorPrimaryCheckGame($gameId,$creator_primary_user_id)
	{
		$oriented = self::where(['game_id' => $gameId,'creator_primary_user_id'=>$creator_primary_user_id])->first();
		if ($oriented) {
			return true;
		}
		return false;
	}

	/**
	 * 验证主接单人ID存不存在
	 */
	public static function checkGainerPrimaryIdById($gainer_primary_user_id)
	{
		$user = User::where(['id' => $gainer_primary_user_id, 'parent_id' => 0])->first();
		if ($user) {
			return true;
		}
		return false;
	}

}
