<?php

namespace App\Jobs\Game;

use App\Models\Game;
use App\Models\Game\Screenshot;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Screenshots implements ShouldQueue
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
		if ($this->game_ids->count() <= 0) return;

		$games = Game::whereIn('id', $this->game_ids)
		             ->select('id')
		             ->get();

		// Don't get any screenshots for games that we don't have
		// in the db
		$this->game_ids->reject(function ($id) use ($games) {
			return !$games->contains($id);
		})->each(function ($id) {
			$screenshot_regex = '/var rgScreenshotURLs = {(.*)};/';

			// Bypass Age Check
			$jar = CookieJar::fromArray([
				'birthtime'      => -189424799,
				'mature_content' => 1,
			], 'store.steampowered.com');

			try {
				$client = new Client();
				$res    = $client->get('http://store.steampowered.com/app/' . $id, [
					'cookies' => $jar,
				]);
			} catch (ClientException $e) {
				\Log::error($e);

				return;
			}

			preg_match($screenshot_regex, $res->getBody()->getContents(), $matches);

			if ($matches) {
				collect(json_decode('{' . $matches[1] . '}'))
					->each(function ($shot) use ($id) {
						$shot = str_replace('_SIZE_', '.1920x1080', $shot);

						$url = substr($shot, 0, stripos($shot, '?'));

						if (empty($url))
							return;

						Screenshot::updateOrCreate([
							'game_id' => $id,
							'url'     => $url,
						], [
							'game_id' => $id,
							'url'     => $url,
						]);
					});
			}

			usleep(floor(rand(500, 2000)));
		});
	}
}
