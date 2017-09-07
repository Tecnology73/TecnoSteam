<?php

namespace App\Models\Forums;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
	protected $fillable = [
		'title',
		'description',
	];

	protected $hidden = [
		'id',
		'created_at',
		'updated_at',
	];

	public function votes()
	{
		return $this->hasMany(SuggestionVote::class);
	}

	public function format()
	{
		$votes   = $this->votes;
		$my_vote = $votes->where('user_id', \Auth::id())->first();

		return [
			'id'          => $this->id,
			'title'       => $this->title,
			'description' => $this->description,
			'votes'       => [
				'up'      => $votes->where('type', 'up')->count(),
				'down'    => $votes->where('type', 'down')->count(),
				'total'   => count($votes),
				'didVote' => (isset($my_vote) ? $my_vote->type : null),
			],
			'createdAt'   => $this->created_at->diffForHumans(),
		];
	}
}
