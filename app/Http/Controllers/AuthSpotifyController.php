<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Socialite;
use SpotifyWebAPI\SpotifyWebAPI;

class AuthSpotifyController extends Controller
{
    /**
     * Redirect the user to the Spotify authentication page
     * 
     */
    public function spotifyLogin()
    {
        return Socialite::driver('spotify')
                        ->scopes(['user-top-read'])
                        ->redirect();
    }

    public function spotifyCallback()
    {
        $user = Socialite::driver('spotify')->user();
        session(['spotify_token' => $user->token]);
        session(['spotify_refresh' => $user->refreshToken]);

        return redirect('/result');
    }

    public function getResult(SpotifyWebAPI $spotify)
    {
        try {
            $top_tracks_short = $spotify->getMyTop('tracks', [
                'limit' => 50,
                'time_range' => 'short_term' // long_term | medium_term | short_term
            ]);

            $top_tracks_medium = $spotify->getMyTop('tracks', [
                'limit' => 50,
                'time_range' => 'medium_term' // long_term | medium_term | short_term
            ]);

            $top_tracks_long = $spotify->getMyTop('tracks', [
                'limit' => 50,
                'time_range' => 'long_term' // long_term | medium_term | short_term
            ]);

            // return ($top_tracks->items);
    
            return view('top', compact('top_tracks_short', 'top_tracks_medium', 'top_tracks_long'));
        }
        catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
            // handle error
            return redirect('/');
        }
    }

    public function getTopTracksAjax(SpotifyWebAPI $spotify, Request $request)
    {
        try {
            $type = $request->query('type', 'short_term');

            $top_tracks = $spotify->getMyTop('tracks', [
                'limit' => 50,
                'time_range' => $type
            ]);
            
            return response()->json($top_tracks);
        }
        catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
            // handle error
            return response()->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
            // return redirect('/');
        }
    }
}
