<?php

namespace App\Jobs\Game;

use App\Models\Game;
use App\Models\Game\Achievement;
use App\Models\Game\Statistic;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Steam;

class Schema implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $game_ids;

	/**
	 * Create a new job instance.
	 *
	 * @param $game_ids
	 */
	public function __construct($game_ids)
	{
		$this->game_ids = $game_ids;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->game_ids->each(function ($id) {
			if (!isset($id) || !Game::where('id', $id)->count()) return;

			$schema = Steam::userStats()->GetSchemaForGame($id, true);

			if (!isset($schema->game->availableGameStats)) return;

			$stats = $schema->game->availableGameStats;

			if (isset($stats->achievements)) {
				collect($stats->achievements)->each(function ($achievement) use ($id) {
					Achievement::updateOrCreate([
						'game_id' => $id,
						'name'    => $achievement->name,
					], [
						'game_id'       => $id,
						'name'          => $achievement->name,
						'display_name'  => $achievement->displayName,
						'default_value' => $achievement->defaultvalue,
						'hidden'        => $achievement->hidden,
						'description'   => $achievement->description ?? null,
						'icon'          => $achievement->icon ?? null,
						'icon_gray'     => $achievement->icongray ?? null,
					]);
				});
			}

			if (isset($stats->stats)) {
				collect($stats->stats)->each(function ($stat) use ($id) {
					Statistic::updateOrCreate([
						'game_id' => $id,
						'name'    => $stat->name,
					], [
						'game_id'       => $id,
						'name'          => $stat->name,
						'default_value' => $stat->defaultvalue,
						'display_name'  => $stat->displayName,
					]);
				});
			}
		});
	}
}
