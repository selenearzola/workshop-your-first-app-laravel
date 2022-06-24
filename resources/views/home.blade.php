<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf_token" content="{{ csrf_token() }}">
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<title>My Collection</title>
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
	<div class="">
		@if (Route::has('login'))
		<div class="hidden fixed top-0 right-0 text-right px-8 py-4 sm:block">
			@auth
			<a href="{{ url('/dashboard') }}" class="text-md font-bold text-stone-400 no-underline ">
				Dashboard
			</a>
			@else
			<a href="{{ route('login') }}" class="text-md font-bold text-stone-400 no-underline">
				Log in
			</a>
			@if (Route::has('register'))
			<a href="{{ route('register') }}" class="ml-4 text-md font-bold text-stone-400 no-underline">
				Register
			</a>
			@endif
			@endauth
		</div>
		@endif
	</div>

	<div id="app"></div>

	<script src="{{ mix('js/app.js') }}"></script>

</body>

</html>