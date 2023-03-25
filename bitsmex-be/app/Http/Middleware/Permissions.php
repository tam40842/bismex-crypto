<?php

namespace App\Http\Middleware;

use Request;
use Closure;
use Auth;
use App\User;
use DB;

class Permissions
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
        $url = Request::url();
        $explode_url = explode('/', $url);
        $role = DB::table('role')->where('slug', $request->user()->permission)->first();
        if(isset($role)) {
            $permissions = json_decode($role->permissions, true);
            if(isset($explode_url[4]) && array_key_exists($explode_url[4], $permissions)) {
                if(!empty($guards) && !in_array($guards[0], $permissions[$explode_url[4]])) {
                    abort(404);
                }
                return $next($request);
            }
        }
        abort(404);
    }
}
