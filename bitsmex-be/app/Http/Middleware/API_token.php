<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Auth;

class API_token
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = request()->bearerToken();
        $token = DB::table('api_token')->where('token', $token)->where('status', 1)->first();
        if (!is_null($token)) {
            $user = DB::table('users')->where('id', $token->user_id)->where('status', 1)->first();
            if (Auth::loginUsingId($user->id)) {
                DB::table('api_token')->where('id', $token->id)->update(['last_access' => now()]);
                return $next($request);
            }
        }
        return response()->json([
            'status' => 404,
            'message' => 'Token does not exist'
        ], 200);
    }
}
