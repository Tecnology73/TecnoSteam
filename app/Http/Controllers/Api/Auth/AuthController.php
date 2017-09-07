<?php

namespace App\Http\Controllers\Api\Auth;

use App\User;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
	public function login()
	{
		$user = User::where('steamid64', request('steamid64'))->first();

		if (!isset($user)) {
			return response()->json([
				'success' => false,
				'message' => 'Nice Try!',
			], 401);
		}

		return response()->json([
			'success' => true,
			'user'    => $user->format(),
		]);
	}
}
