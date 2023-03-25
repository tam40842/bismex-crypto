<?php
namespace App\Http\Controllers\Vuta;

use Exception;

class Device {

	public static function getOS($agent = '') {
		$os_platform  = "Unknown OS Platform";
		$default_agent = ($agent == '') ? request()->server('HTTP_USER_AGENT') : $agent;
		$os_array     = array(
			  '/windows nt 10/i'      =>  'Windows 10',
			  '/windows nt 6.3/i'     =>  'Windows 8.1',
			  '/windows nt 6.2/i'     =>  'Windows 8',
			  '/windows nt 6.1/i'     =>  'Windows 7',
			  '/windows nt 6.0/i'     =>  'Windows Vista',
			  '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
			  '/windows nt 5.1/i'     =>  'Windows XP',
			  '/windows xp/i'         =>  'Windows XP',
			  '/windows nt 5.0/i'     =>  'Windows 2000',
			  '/windows me/i'         =>  'Windows ME',
			  '/win98/i'              =>  'Windows 98',
			  '/win95/i'              =>  'Windows 95',
			  '/win16/i'              =>  'Windows 3.11',
			  '/macintosh|mac os x/i' =>  'Mac OS X',
			  '/mac_powerpc/i'        =>  'Mac OS 9',
			  '/linux/i'              =>  'Linux',
			  '/ubuntu/i'             =>  'Ubuntu',
			  '/iphone/i'             =>  'iPhone',
			  '/ipod/i'               =>  'iPod',
			  '/ipad/i'               =>  'iPad',
			  '/android/i'            =>  'Android',
			  '/blackberry/i'         =>  'BlackBerry',
			  '/webos/i'              =>  'Mobile'
		);

		foreach ($os_array as $regex => $value)
			if (preg_match($regex, $default_agent))
				$os_platform = $value;

		return $os_platform;
	}

	public static function getOS_icon($user_agent) { 
		$os_platform  = "fa-bullseye";
		$os_array     = array(
			  '/windows nt 10/i'      =>  'fa-windows',
			  '/windows nt 6.3/i'     =>  'fa-windows',
			  '/windows nt 6.2/i'     =>  'fa-windows',
			  '/windows nt 6.1/i'     =>  'fa-windows',
			  '/windows nt 6.0/i'     =>  'fa-windows',
			  '/windows nt 5.2/i'     =>  'fa-windows',
			  '/windows nt 5.1/i'     =>  'fa-windows',
			  '/windows xp/i'         =>  'fa-windows',
			  '/windows nt 5.0/i'     =>  'fa-windows',
			  '/windows me/i'         =>  'fa-windows',
			  '/win98/i'              =>  'fa-windows',
			  '/win95/i'              =>  'fa-windows',
			  '/win16/i'              =>  'fa-windows',
			  '/macintosh|mac os x/i' =>  'fa-apple',
			  '/mac_powerpc/i'        =>  'fa-apple',
			  '/linux/i'              =>  'fa-linux',
			  '/ubuntu/i'             =>  'fa-linux',
			  '/iphone/i'             =>  'fa-apple',
			  '/ipod/i'               =>  'fa-apple',
			  '/ipad/i'               =>  'fa-apple',
			  '/android/i'            =>  'fa-android'
		);
		foreach($os_array as $regex => $value) {
			if (preg_match($regex, $user_agent)) {
				$os_platform = $value;
			}
		}

		return $os_platform;
	}

	public static function getBrowser() {
		$browser        = "Unknown Browser";
		$browser_array = array(
			'/msie/i'      => 'Internet Explorer',
			'/firefox/i'   => 'Firefox',
			'/safari/i'    => 'Safari',
			'/chrome/i'    => 'Chrome',
			'/edge/i'      => 'Edge',
			'/opera/i'     => 'Opera',
			'/netscape/i'  => 'Netscape',
			'/maxthon/i'   => 'Maxthon',
			'/konqueror/i' => 'Konqueror',
			'/mobile/i'    => 'Handheld Browser',
			'/coc_coc_browser/i'    => 'Cốc Cốc',
			'/OPR/i'    => 'Opera'
	 );

		foreach ($browser_array as $regex => $value)
			if (preg_match($regex, request()->server('HTTP_USER_AGENT')))
				$browser = $value;

		return $browser;
	}

	public static function getBrowser_icon($agent = '') {
		$browser        = "fa-globe";
		$default_agent = ($agent == '') ? request()->server('HTTP_USER_AGENT') : $agent;
		$browser_array = array(
			'/msie/i'      => 'fa-internet-explorer',
			'/firefox/i'   => 'fa-firefox',
			'/safari/i'    => 'fa-safari',
			'/chrome/i'    => 'fa-chrome',
			'/edge/i'      => 'fa-edge',
			'/opera/i'     => 'fa-opera',
			'/netscape/i'  => 'fa-globe',
			'/maxthon/i'   => 'fa-globe',
			'/konqueror/i' => 'fa-globe',
			'/mobile/i'    => 'fa-globe',
			'/coc_coc_browser/i'    => 'fa-chrome',
			'/OPR/i'    => 'fa-opera'
	 );

		foreach ($browser_array as $regex => $value) {
			if(preg_match($regex, $default_agent)) {
				$browser = $value;
			}
		}
		return $browser;
	}

	public static function getLocationDetail($ip = null) {
		$ip_address = !is_null($ip) ? $ip : request()->server('REMOTE_ADDR');
		$data = null;
		try {
			$data = json_decode(file_get_contents("http://extreme-ip-lookup.com/json/".$ip_address));
		} catch(Exception $e) {
			//nén lỗi ngoại lệ của biến e ra (không trả về return $data)
			throw($e);
		}
		return $data;
	}
}