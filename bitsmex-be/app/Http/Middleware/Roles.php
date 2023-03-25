<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use JWTAuth;
use JWTAuthException;
use App\User;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if(Auth::check()) {
            if(Auth::user()){
                if(!empty($guards)){
                    $logged_in = true;
                    $roles = json_decode(Auth::user()->roles);
                    if(!empty($roles)){
                        foreach($guards as $value){
                            if(in_array($value, $roles)){
                                $logged_in = true;
                                break;
                            }else{
                                $logged_in = false;
                            }
                        }
                        if($logged_in == false){
                            abort('404');
                        }
                    }else{
                        Auth::logout();
                        abort('404');
                    }
                }
            }else{
                Auth::logout();
                abort('404');
            }
            return $next($request);
        }
        abort('404');
    }
}