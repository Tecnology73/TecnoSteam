<?php

namespace App\Models\Game;

use App\Models\Game;
use Golonka\BBCode\BBCodeParser;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	protected $fillable = [
		'id',
		'game_id',
		'title',
		'content',
		'url',
		'external',
		'author',
		'label',
		'posted',
	];

	protected $hidden = [
		'id',
		'game_id',
	];

	protected $casts = [
		'external' => 'boolean',
	];

	protected $dates = [
		'posted',
	];

	public $incrementing = false;
	public $timestamps   = false;

	public function game()
	{
		return $this->belongsTo(Game::class);
	}

	public function format($include_game = false)
	{
		$content = (new BBCodeParser())->parse($this->content);

		$content = preg_replace_callback("|\[h(\d+)\]([^>]+)\[/h(\d+)\]|", function ($matches) {
			return '<h' . $matches[1] . '>' . $matches[2] . '</h' . $matches[1] . '>';
		}, $content);

		$data = [
			'id'         => $this->id,
			'title'      => $this->title,
			'content'    => $content,
			'url'        => $this->url,
			'author'     => $this->author,
			'label'      => $this->label,
			'posted'     => $this->posted->format('Y-m-d H:i:s\Z'),
			'isExternal' => $this->external,
		];

		if ($include_game)
			$data['game'] = $this->game->format();

		return $data;
	}
}
