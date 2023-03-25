<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Vuta\Vuta;
use Carbon\Carbon;
use DB;

class Maintenance
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
        $settings = Vuta::get_settings(['is_maintenance', 'maintenance_content', 'maintenance_allowed_ip', 'maintenance_expired']);
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $allowed_ip = json_decode($settings['maintenance_allowed_ip'], true);
        $client_ip = @request()->server('HTTP_CF_CONNECTING_IP') ? request()->server('HTTP_CF_CONNECTING_IP') : request()->server('REMOTE_ADDR');
        if(!in_array($client_ip, $allowed_ip)) {
            if($settings['is_maintenance'] == true && $now < $settings['maintenance_expired']) {
                if($request->header('Accept') == "application/json"){
                    return response()->json([
                        'status' => 503,
                        'message' => strip_tags(($settings['maintenance_content']))
                    ],503);
                } else{
                    $maintenance = (string) view('maintenance')->render();
                    return response($maintenance, 503);
                }
            }
        }
        return $next($request);
    }
}
