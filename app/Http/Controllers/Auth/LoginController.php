<?php

namespace App\Http\Controllers\Auth;

use App\Jobs\User\Friends;
use App\Jobs\User\Summary;
use App\Jobs\UserLibrary;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
	public function login()
	{
		return Socialite::driver('steam')->redirect();
	}

	public function callback()
	{
		$steam_user = Socialite::driver('steam')->user();

		if (!isset($steam_user)) {
			return response()->json([
				'message' => 'Woops!',
			], 400);
		}

		$user = User::updateOrCreate([
			'steamid64' => $steam_user->user['steamid'],
		], [
			'steamid64'   => $steam_user->id,
			'name'        => $steam_user->name,
			'nickname'    => $steam_user->nickname,
			'avatar'      => $steam_user->user['avatarfull'],
			'countrycode' => $steam_user->user['loccountrycode'],
		]);

		//$token = $user->createToken('Token')->accessToken;

		/*dispatch((new Summary($user->id, $user->steamid64))->onQueue('veryhigh'));
		dispatch((new UserLibrary($user))->onQueue('veryhigh')->delay(Carbon::now()->addSecond()));
		dispatch((new Friends($user))->onQueue('veryhigh')->delay(Carbon::now()->addSeconds(2)));*/

		\Auth::login($user);

		if (\Auth::check())
			return response()->redirectToRoute('home');

		return response('Woops!', 400);
	}

	public function logout()
	{
		\Auth::logout();

		return response()->redirectToRoute('home');
	}
}
