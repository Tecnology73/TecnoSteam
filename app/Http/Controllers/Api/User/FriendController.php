<?php

namespace App\Http\Controllers\Api\User;

use App\User;
use App\Http\Controllers\Controller;

class FriendController extends Controller
{
	public function list()
	{
		$user    = \Auth::user();
		$friends = $user->friends->transform(function ($friend) {
			return $friend->format();
		});

		return response()->json($friends);
	}
}
