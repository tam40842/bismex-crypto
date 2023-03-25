<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use JWTAuth;
use JWTAuthException;

class CheckLogin
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
        if($request->has('token')) {
            $response = $next($request);
            try
            {
                if (! $user = JWTAuth::parseToken()->authenticate() )
                {
                    return ApiHelpers::ApiResponse(101, null);
                }
            }
            catch (TokenExpiredException $e)
            {
                // If the token is expired, then it will be refreshed and added to the headers
                try
                {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    $response->header('Authorization', 'Bearer ' . $refreshed);
                }
                catch (JWTException $e)
                {
                    return ApiHelpers::ApiResponse(103, null);
                }
                $user = JWTAuth::setToken($refreshed)->toUser();
            }
            catch (JWTException $e)
            {
                return ApiHelpers::ApiResponse(101, null);
            }
            // Login the user instance for global usage
            Auth::login($user, true);
            return $response;
        }
        return $next($request);
    }
}
