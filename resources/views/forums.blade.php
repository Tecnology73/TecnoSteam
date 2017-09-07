@extends ('layouts.app')

@section('content')
	<div class="container">
		<div class="header">
			<p>You thought this was the forums? THINK AGAIN!!!</p>
			<p>Submit/Vote for your favourite features below :)</p>

			<button class="btn submit-suggestion" onClick="toggleSuggestionPopup()">Submit Suggestion</button>
		</div>

		<div class="suggestions-group">
			@foreach($suggestions as $suggestion)
				@if(isset($suggestion['filler']))
					<div class="suggestion-item empty"></div>
				@else
					<div class="suggestion-item">
						<div class="vote-group">
							<div class="vote-up {{ $suggestion['votes']['didVote'] === 'up' ? 'self' : '' }}">
								<a href="/forums/{{ $suggestion['id'] }}/vote/up">
									<span class="fa fa-thumbs-up"></span>
								</a>
							</div>

							<div class="vote-counter {{ $suggestion['votes']['up'] > $suggestion['votes']['down'] ?
							'positive' : 'negative' }}">
								{{ $suggestion['votes']['up'] - $suggestion['votes']['down'] }}
							</div>

							<div class="vote-down {{ $suggestion['votes']['didVote'] === 'down' ? 'self' : '' }}">
								<a href="/forums/{{ $suggestion['id'] }}/vote/down">
									<span class="fa fa-thumbs-down"></span>
								</a>
							</div>
						</div>

						<div class="info-group">
							<div class="title">{{ $suggestion['title'] }}</div>
							<div class="description">{{ $suggestion['description'] }}</div>
						</div>

						<div class="timestamp">{{ $suggestion['createdAt'] }}</div>
					</div>
				@endif
			@endforeach

			<div class="pagination">
				<div class="pagination-previous {{ !isset($previousPage) ? 'disabled' : '' }}">
					<a href="{{ $previousPage }}">
						<span class="fa fa-angle-double-left"></span>
						Previous
					</a>
				</div>

				<div class="pagination-next {{ !isset($nextPage) ? 'disabled' : '' }}">
					<a href="{{ $nextPage }}">
						Next
						<span class="fa fa-angle-double-right"></span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="suggestion-popup" id="suggestionPopup">
		<div class="content">
			<form action="/forums" method="post">
				{{ csrf_field() }}
				<h2 style="text-align: center;">What's your Suggestion?</h2>

				<div class="form-group">
					<input type="text" class="form-control form-control-block" placeholder="Title" name="title">

					<div class="requirements">
						Min: 5 | Max: 255
					</div>

					@if($errors->has('title'))
						<div class="error">{{ $errors->first('title') }}</div>
					@endif
				</div>

				<div class="form-group">
					<textarea class="form-control form-control-block" placeholder="Description"
					          name="description"></textarea>

					<div class="requirements">
						Min: 50 | Max: 65,535
					</div>

					@if($errors->has('description'))
						<div class="error">{{ $errors->first('description') }}</div>
					@endif
				</div>

				<div class="form-group" style="margin-top: 2em;">
					<button type="submit" class="btn">Submit</button>

					<button type="button" class="btn btn-underline" onClick="toggleSuggestionPopup('hide')">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('css')
	<link rel="stylesheet" href="/css/forums.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/698a8adccc.css">
@endsection

@section('js')
	<script>
		function toggleSuggestionPopup(state = null) {
			const $popup = $('#suggestionPopup');

			if (state !== null) {
				$popup.fadeToggle(state === 'show');
				$popup.toggleClass('visible', state === 'show');
				return;
			}

			$popup.fadeToggle($popup.hasClass('visible'));
			$popup.toggleClass('visible', !$popup.hasClass('visible'));
		}
	</script>

	@if($errors->any())
		<script>(function () {
				toggleSuggestionPopup();
			})();</script>
	@endif
@endsection
