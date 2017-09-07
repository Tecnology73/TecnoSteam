<?php

namespace App\Models\Forums;

use Illuminate\Database\Eloquent\Model;

class SuggestionVote extends Model
{
	protected $fillable = [
		'user_id',
		'suggestion_id',
		'type',
	];

	public $timestamps = false;

	public function suggestion()
	{
		return $this->belongsTo(Suggestion::class);
	}
}
