# TecnoSteam

This is the backend for [TecnoSteam Client](https://github.com/73cn0109y/TecnoSteamClient).

## Setup
Create a duplicate of `.env.example` and rename it to `.env`
At the bottom of the `.env` file add the following;
```
STEAM_KEY=S0M3R4ND0MK3Y7H47W0N7W0RK
STEAM_REDIRECT_URI=http://tecnosteam.dev/login/callback
```

You can get a Steam API Key from [here](http://steamcommunity.com/dev/apikey).
You should also change the domain for the STEAM_REDIRECT_URI to whatever you have locally. This URL MUST match what you have set when obtaining a Steam API Key!

Run the following commands;
```
composer install
php artisan migrate
php artisan passport:install
npm install
npm run dev
```

Open your TecnoSteam Client project and inside `src/Store/utils/api.js`, change API_ROOT so it has the same domain as what you set above with STEAM_REDIRECT_URI. If your STEAM_REDIRECT_URI is `http://steamclient.dev/login/callback` then your API_ROOT should be `http://steamclient.dev/api/`

You'll also need to open `routes/web.php` inside this project, uncomment lines 14-16 (should be the route named "test") and in your browser navigate to that page (e.g. http://steamclient.dev/test). This page will generate an access token for the client to use. Copy this token, open `src/Store/utils/api.js` in the TecnoSteam Client project, go to line 10 and find `[INSERT PASSPORT TOKEN HERE]`. Replace that text with the token you just copied. You will need to re-compile the TecnoSteam Client project!
