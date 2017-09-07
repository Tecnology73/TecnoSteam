<?php

namespace App\Jobs\Game;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class News implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $game_id;

	/**
	 * Create a new job instance.
	 *
	 * @param $game_id
	 */
	public function __construct($game_id)
	{
		$this->game_id = $game_id;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$news = \Steam::news()->GetNewsForApp($this->game_id)->newsitems;

		collect($news)->each(function ($item) {
			if (!Game::where('game_id', $this->game_id)->count())
				return;

			News::updateOrCreate([
				'id' => $item->gid,
			], [
				'id'       => $item->gid,
				'game_id'  => $this->game_id,
				'title'    => $item->title,
				'content'  => $item->contents,
				'url'      => $item->url,
				'external' => $item->is_external_url,
				'author'   => $item->author,
				'label'    => $item->feedlabel,
				'posted'   => Carbon::createFromTimestampUTC($item->date),
			]);
		});
	}
}
