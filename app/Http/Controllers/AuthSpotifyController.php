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
                        ->scopes(['user-top-read', 'playlist-modify-public'])
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

    public function postCreatePlaylist(SpotifyWebAPI $spotify, Request $request)
    {
        try {
            $type = $request->input('type', 'short_term');

            switch ($type) {
                case 'short_term':
                    $type_label = 'Last 4 Weeks';
                    break;
                
                case 'medium_term':
                    $type_label = 'Last 4 Months';
                    break;

                case 'long_term':
                    $type_label = 'Last Couple of Years';
                    break;

                default:
                    $type_label = '';
                    break;
            }

            $playlist_name = $request->input('title', 'Your Top Songs ' . $type_label);

            $top_tracks = $spotify->getMyTop('tracks', [
                'limit' => 50,
                'time_range' => $type
            ]);

            $top_tracks_id = collect($top_tracks->items)->pluck('id');

            $me = $spotify->me();

            $new_playlist = $spotify->createUserPlaylist($me->id, [
                'name' => $playlist_name
            ]);

            $spotify->addUserPlaylistTracks($me->id, $new_playlist->id, $top_tracks_id->toArray());
            // $spotify->addUserPlaylistTracks($me->id, '2C8NJE5XwsC0MDXkBQa9L4', $top_tracks_id->toArray());
            return response()->json($new_playlist);
        }
        catch(\SpotifyWebAPI\SpotifyWebAPIException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => $e->getCode(),
                    'message' => $e->getMessage()
                ]);
            } else {
                return redirect('/');
            }
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
