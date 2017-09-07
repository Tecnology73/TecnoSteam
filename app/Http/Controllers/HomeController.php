<?php

namespace App\Http\Controllers;

use App\Jobs\UserLibrary;
use App\Models\Game;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class HomeController extends Controller
{
	public function index()
	{
		if (\Auth::check())
			return view('home', [
				'user' => \Auth::user()->format(),
			]);

		return view('welcome');
	}
}
