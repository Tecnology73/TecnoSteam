<?php

namespace App\Models\User\Library;

use App\Models\Game;
use Illuminate\Database\Eloquent\Model;

class GameCategory extends Model
{
	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function game()
	{
		return $this->belongsTo(Game::class);
	}
}
