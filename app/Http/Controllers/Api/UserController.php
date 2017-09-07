<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
	public function info()
	{
		$user = \Auth::user();

		return response()->json($user->format());
	}
}
