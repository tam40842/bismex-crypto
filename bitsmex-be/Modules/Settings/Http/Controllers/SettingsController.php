<?php

namespace Modules\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Vuta\Vuta;
use DB;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    use Vuta;

    public function index()
    {
        $settings = $this->get_settings(['is_website_notice', 'website_notice', 'transfer_fee', 'is_website_notice_deposit', 'website_notice_deposit']);
        $settings['languages'] = [
            'en' => [
                'flag' => url('images/flags/united-states-of-america.png'),
                'name' => 'English'
            ],
            'vi' => [
                'flag' => url('images/flags/vietnam.png'),
                'name' => 'Vietnamese'
            ],
        ];
        $settings['website_notice'] = strip_tags(($settings['website_notice']));
        $settings['website_notice_deposit'] = strip_tags(($settings['website_notice_deposit']));
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $settings
        ]);
    }

    public function countries() {
        $countries = DB::table('countries')->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Get data is success.',
            'data' => $countries
        ]);
    }
}
