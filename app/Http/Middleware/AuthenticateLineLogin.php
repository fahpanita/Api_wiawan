<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Concerns\InteractsWithInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthenticateLineLogin
{

    public function handle(Request $request, Closure $next)
    {
        $token = request()->bearerToken();
        $idToken = $request->header('X-Line-Id-Token');


        if (!$token) {

            return response("Not Found Token", 400);
        }

        if (!$idToken) {

            return response("Not Found idToken", 400);
        }

        $response = Http::get('https://api.line.me/oauth2/v2.1/verify', [
            'access_token' => $token,
        ]);

        $responseIdToken = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://api.line.me/oauth2/v2.1/verify', [
            'id_token' => $idToken,
            'client_id' => env("LINE_CLIENT_ID"),
        ]);

        if ($response->failed()) {
            return response("unauthenticate", 400);
        }

        $responseProfile = Http::withHeaders([
            'Authorization' => "Bearer " . $token,
        ])->get('https://api.line.me/v2/profile');

        $user = User::where("line_id", $responseProfile["userId"])->first();

        if (!$user) {
            //return response("Not Found User", 400);
            $userModel = new User();
            $userModel->line_id = $responseProfile["userId"];
            $userModel->name = $responseProfile["displayName"];
            $userModel->email = $responseIdToken["email"];
            $userModel->picture = $responseProfile["pictureUrl"];
            $userModel->role = 'user';

            $userModel->save();
        };

        $user = User::where("line_id", $responseProfile["userId"])->first();

        Auth::login($user);


        //return response($users, 400);

        //return response($responseProfile->json(), 400);
        //return redirect();
        return $next($request);
    }
}
