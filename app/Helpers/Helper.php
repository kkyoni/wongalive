<?php

namespace App\Helpers;
use DateTime;
use App\Models\BlockUserList;
use Auth;

class Helper {


    // % calculation
	public static function ValueInPer($baseAmount,$totalAmount) {
		$count = 0;
		if($baseAmount != 0){
			$data= ($baseAmount * 100 );
			$count= ($data / $totalAmount);         
			$count=number_format((float)$count, 2, '.', '');
		}
		return $count.'%';    

	}
    // % calculation


	public static function TimeToMin($min = 0) {
		return 0;  

	}

	public static function CheckBlockUser($user) {
		$blockuser = BlockUserList::where('user_id',$user->id)->pluck('blocked_user_id');
		return $blockuser;
	}

	public static function SendCheckBlockUser($user) {
		$sendblockuser = BlockUserList::where('blocked_user_id',$user->id)->pluck('user_id');
		return $sendblockuser;
	}

}
