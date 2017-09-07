<?php

namespace App\Models;

use App\Models\Game\Achievement;
use App\Models\Game\News;
use App\Models\Game\Screenshot;
use App\Models\Game\Statistic;
use App\Models\User\Library\Category;
use App\Models\User\Library\GameCategory;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
	protected $casts = [
		'developers' => 'array',
		'publishers' => 'array',
		'platforms'  => 'array',
		'categories' => 'array',
		'genres'     => 'array',
	];

	protected $dates = [
		'release',
	];

	protected $fillable = [
		'id',
		'name',
		'controller_support',
		'description',
		'icon',
		'logo',
		'header',
		'about',
		'website',
		'developers',
		'publishers',
		'price',
		'price_currency',
		'price_discount',
		'platforms',
		'categories',
		'genres',
		'release',
	];

	public $timestamps   = false;
	public $incrementing = false;

	public function owners()
	{
		return $this->belongsToMany(User::class, 'game_users', 'game_id', 'user_id');
	}

	public function achievements()
	{
		return $this->hasMany(Achievement::class);
	}

	public function screenshots()
	{
		return $this->hasMany(Screenshot::class);
	}

	public function news()
	{
		return $this->hasMany(News::class);
	}

	public function userCategories()
	{
		return $this->hasMany(GameCategory::class)->with('category');
	}

	public function format($include_screenshots = false, $include_achievements = false)
	{
		$userCategories = $this->userCategories->transform(function ($category) {
			return $category->category->format(false);
		});

		$data = [
			'id'                => $this->id,
			'name'              => $this->name,
			'controllerSupport' => $this->controller_support,
			'description'       => $this->description,
			'icon'              => $this->icon,
			'logo'              => $this->logo,
			'header'            => $this->header,
			'about'             => $this->about,
			'website'           => $this->website,
			'developers'        => $this->developers,
			'publishers'        => $this->publishers,
			'price'             => [
				'initial'  => $this->price,
				'final'    => $this->price,
				'currency' => $this->price_currency,
				'discount' => $this->price_discount,
			],
			'platforms'         => $this->platforms,
			'categories'        => $this->categories,
			'userCategories'    => $userCategories,
			'genres'            => $this->genres,
			'release'           => $this->release->format('Y-m-d H:i:s\Z'),
		];

		if ($include_screenshots) {
			$data['screenshots'] = $this->screenshots->transform(function ($screenshot) {
				return $screenshot->url;
			});
		}

		if ($include_achievements) {
			$data['achievements'] = $this->achievements->transform(function ($achievement) {
				return $achievement->format();
			});
		}

		return $data;
	}

	public static function fromAppDetails($game)
	{
		$developers   = $game->developers ?? collect();
		$publishers   = $game->publishers ?? collect();
		$categories   = $game->categories ?? collect();
		$genres       = $game->genres ?? collect();
		$release_date = (!isset($game->release->data) ? null : Carbon::createFromFormat(strpos($game->release->date, ',') ? 'j M, Y' : 'M Y',
			$game->release->date));

		$db_game = Game::updateOrCreate([
			'id' => $game->id,
		], [
			'id'                 => $game->id,
			'name'               => $game->name,
			'controller_support' => $game->controllerSupport,
			'description'        => $game->description,
			'icon'               => $game->icon,
			'logo'               => $game->logo,
			'header'             => $game->header,
			'about'              => $game->about,
			'website'            => $game->website,
			'developers'         => $developers->toArray(),
			'publishers'         => $publishers->toArray(),
			'price'              => $game->price->initial ?? 0,
			'price_currency'     => $game->price->currency ?? 'USD',
			'price_discount'     => $game->price->discount_percent ?? 0,
			'platforms'          => [
				'windows' => $game->platforms->windows,
				'mac'     => $game->platforms->mac,
				'linux'   => $game->platforms->linux,
			],
			'categories'         => $categories->transform(function ($category) {
				return $category->description;
			})->all(),
			'genres'             => $genres->transform(function ($genre) {
				return $genre->description;
			})->all(),
			'release'            => $release_date,
		]);

		return $db_game;
	}
}
