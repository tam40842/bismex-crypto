<?php
namespace App;

class VutaHelpers {
    
    public static function ipClient() {
        $cf_ip = request()->server('HTTP_CF_CONNECTING_IP');
        $client_ip = !is_null($cf_ip) ? $cf_ip : request()->server('REMOTE_ADDR');
        return $client_ip;
    }
}