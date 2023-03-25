<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Auth;
use Carbon\Carbon;
use Gate;

class SettingController extends Controller
{
    public function index() {
        Gate::allows('modules', 'settings_access');

        $data = [
            'settings' => $this->get_settings(['title_website', 'site_email', 'site_phone', 'site_logo', 'site_favicon', 'password_backup', 'profit_reset_time'])
        ];
        return view('admin.settings.index', $data);
    }

    public function SaveSettings(Request $request) {
        Gate::allows('modules', 'settings_access');

        $settings = [
            'title_website' => $request->title_website,
            'site_email' => $request->site_email,
            'site_phone' => $request->site_phone,
            'site_address' => $request->site_address,
            'password_backup' => Hash::make($request->password_backup),
            'site_logo' => $request->site_logo,
            'site_favicon' => $request->site_favicon,
        ];
        foreach($settings as $key => $value) {
            DB::table('settings')->where('setting_name', $key)->update(['setting_value' => $value]);
        }
        return redirect()->back()->with('alert_success', 'Settings update successfully.');
    }

    public function getNotice() {
        Gate::allows('modules', 'settings_notice_access');

        $data = [
            'setting' => $this->get_settings(['is_maintenance', 'maintenance_content', 'is_website_notice', 'website_notice', 'maintenance_allowed_ip', 'maintenance_expired'])
        ];
        return view('admin.settings.notice', $data);
    }

    public function postNotice(Request $request) {
        Gate::allows('modules', 'settings_notice_access');

        $format_maintenance_expired = Carbon::parse($request->maintenance_expired)->format('Y-m-d H:i:s');
        $maintenance_allowed_ip = !is_null($request->maintenance_allowed_ip) ? explode(', ', $request->maintenance_allowed_ip) : [];
        $settings = [
            'is_maintenance' => $request->is_maintenance,
            'maintenance_content' => $request->maintenance_content,
            'is_website_notice' => $request->is_website_notice,
            'website_notice' => $request->website_notice,
            'maintenance_allowed_ip' => json_encode($maintenance_allowed_ip),
            'maintenance_expired' => $format_maintenance_expired,
        ];
        foreach($settings as $key => $value) {
            DB::table('settings')->where('setting_name', $key)->update(['setting_value' => $value]);
        }
        return redirect()->back()->with('alert_success', 'Settings update successfully.');
    }

    public function getNoticeDeposit() {
        Gate::allows('modules', 'settings_notice_deposit_access');

        $data = [
            'setting' => $this->get_settings(['is_website_notice_deposit', 'website_notice_deposit'])
        ];
        return view('admin.settings.deposit', $data);
    }

    public function postNoticeDeposit(Request $request) {
        $settings = [
            'is_website_notice_deposit' => $request->is_website_notice_deposit,
            'website_notice_deposit' => $request->website_notice_deposit,
        ];
        foreach($settings as $key => $value) {
            DB::table('settings')->where('setting_name', $key)->update(['setting_value' => $value]);
        }
        return redirect()->back()->with('alert_success', 'Settings update successfully.');
    }

    public function getSeo() {
        Gate::allows('modules', 'settings_seo_access');

        $data = [
            'setting' => $this->get_settings(['site_facebook', 'tawk_to_id', 'site_description', 'site_keywords', 'site_default_thumbnail', 'google_analytics', 'site_twitter', 'site_telegram'])
        ];
        return view('admin.settings.seo', $data);
    }

    public function postSeo(Request $request) {
        Gate::allows('modules', 'settings_seo_access');

        $settings = [
            'site_telegram' => $request->site_telegram,
            'site_twitter' => $request->site_twitter,
            'site_facebook' => $request->site_facebook,
            'tawk_to_id' => $request->tawk_to_id,
            'site_description' => $request->site_description,
            'site_keywords' => $request->site_keywords,
            'site_default_thumbnail' => $request->site_default_thumbnail,
            'google_analytics' => $request->google_analytics,
        ];
        foreach($settings as $key => $value) {
            DB::table('settings')->where('setting_name', $key)->update(['setting_value' => $value]);
        }
        return redirect()->back()->with('alert_success', 'Settings update successfully.');
    }
    public function getMenu($menu_id = 1) {
        Gate::allows('modules', 'settings_menu_access');

        $data = [
            'menu' => DB::table('menus')->where('menu_id', $menu_id)->first(),
            'menus' => DB::table('menus')->orderBy('menu_id', 'asc')->get(),
            'menu_items' => $this->get_menu_items($menu_id),
        ];
        
        if(empty($data['menu'])) {
            abort('404');
        }
        return view('admin.settings.menu', $data);
    }

    public function postMenu(Request $request, $menu_id = 1) {
        Gate::allows('modules', 'settings_menu_access');

        $menu_item_name = $request->menu_item_name;
        $menu_item_link = $request->menu_item_link;
        $menu_item_blank = $request->menu_item_blank;
        $not_in_id_array = [];
        if(count($menu_item_name) > 0){
            $n = 0;
            foreach($menu_item_name as $key => $value){
                $target_blank = empty($menu_item_blank[$key]) ? 0 : 1;
                $not_in_id_array[] = $key;
                if($menu_item_link[$key]) {
                    $this->update_menu_item($key, [
                        'menu_id' => $menu_id,
                        'menu_item_name' => $menu_item_name[$key],
                        'menu_item_link' => $menu_item_link[$key],
                        'menu_item_blank' => $target_blank,
                    ]);
                    $this->update_menu_item_sort_order($key, $n++);
                } else {
                    return redirect()->back()->with('alert_error', 'Xin vui lòng nhập liên kết cho menu <b>'.$menu_item_name[$key].'</b>');
                }
            }
        }
        
        $this->reset_menu_items($menu_id, $not_in_id_array);
        $menu_items_data = json_decode($request->menu_output);
        if(!empty($menu_items_data)) {
            $this->add_level_menu($menu_items_data);
        }
        return redirect()->back()->with('alert_success', 'Cập nhật menu thành công.');
    }

    public function add_level_menu($menu_items_data, $parent = 0) {
        Gate::allows('modules', 'settings_menu_access');

        if(!empty($menu_items_data)) {
            foreach($menu_items_data as $key => $value) {
                DB::table('menu_items')->where('menu_item_id', $value->id)->update([
                    'menu_item_parent' => $parent
                ]);
                $children = !empty($value->children) ? $value->children : [];
                $this->add_level_menu($children, $value->id);
            }
        }
    }

    public function postAddMenuItem(Request $request) {
        Gate::allows('modules', 'settings_menu_access');
        
        $menu_id = $request->menu_id;
        $menu_item_name = $request->menu_item_name;
        $menu_item_link = $request->menu_item_link;
        $menu_item_id = $this->add_menu_item([
            'menu_id' => $menu_id,
            'menu_item_parent' => 0,
            'sort_order' => 0,
            'menu_item_name' => $menu_item_name,
            'menu_item_link' => $menu_item_link,
            'menu_item_class' => '',
            'menu_item_icon' => '',
            'menu_item_blank' => 0,
        ]);
        $data = '<li class="dd-item" data-id="' . $menu_item_id . '">
            <div class="dd-handle">
                <span class="drag-indicator"></span>
                <div> ' . $menu_item_name . ' </div>
                <div class="dd-nodrag btn-group ml-auto ">
                <button class="btn btn-sm btn-secondary open_menu_modify text-primary"><i class="fa fa-pencil"></i></button> 
                </div>
            </div>
            <div class="modify_menu_item p-3" style="display: none;">
                <div class="form-group">
                    <label><i>Tên menu</i></label>
                    <div class="form-line">
                    <input type="text" name="menu_item_name[' . $menu_item_id . ']" class="form-control form-control-sm" placeholder="Tên menu" value="' . $menu_item_name . '">
                    </div>
                </div>
                <div class="form-group">
                    <label><i>Liên kết đến menu</i></label>
                    <div class="form-line">
                    <input type="text" name="menu_item_link[' . $menu_item_id . ']" class="form-control form-control-sm" placeholder="Liên kết đến menu" value="' . $menu_item_link . '">
                    </div>
                </div>
                <hr>
                <p class="text-right">
                    <a class="close_menu_modify" href="javascript:void(0);" onclick="event.preventDefault();"><i  class="fa fa-check fa-fw text-success"></i> Done</a>
                </p>
            </div>
        </li>';
        return $data;
    }

    public function getSetupPass() {
        $listSetPass = DB::table('setup_pass')->orderBy('id', 'desc')->paginate(10);
        $userSetPass = DB::table('setup_pass')->orderBy('id', 'desc')->first();
        $data = [
            'listSetPass' => $listSetPass,
            'userSetPass' => $userSetPass,
        ];

        return view('admin.settings.setupPass', $data);
    }

    public function postSetupPass(Request $request) {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
        ]);
        
        DB::table('setup_pass')->insert([
            'email' => Auth::user()->email,
            'password' => Hash::make($request->password),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('alert_success', 'Thay đổi mật khẩu cho tất cả thành viên thành công.');
    }

    public function profit_reset_time() {
        DB::table('settings')->where('setting_name', 'profit_reset_time')->update([
            'setting_value' => date(now())
        ]);
        return redirect()->back()->with('alert_success', 'Reset lợi nhuận sàn thành công.');
    }

    public function postTransferLimit(Request $request) {
        $this->validate($request, [
            'value' => 'required|min:1|numeric'
        ]);
        if($request->key == 'transfer_min' || $request->key == 'transfer_max') {
            $settings = $this->get_settings(['transfer_limit']);
            $transfer_limit = $settings['transfer_limit'];
            $transfer_limit = explode(';', $transfer_limit);
            $transfer_min = $transfer_limit[0];
            $transfer_max = $transfer_limit[1];
            if($request->key == 'transfer_min') {
                $transfer_min = $request->value;
            }else if($request->key == 'transfer_max'){
                $transfer_max = $request->value;
            }

            $transfer_limit = implode(';', [$transfer_min, $transfer_max]);
            DB::table('settings')->where('setting_name', 'transfer_limit')->update([
                'setting_value' => $transfer_limit
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật giới hạn chuyển tiền hệ thống thành công.',
            ]);
        }else if($request->key == 'trade_min' || $request->key == 'trade_max') {
            $settings = $this->get_settings(['trade_range']);
            $trade_range = $settings['trade_range'];
            $trade_range = explode(';', $trade_range);
            $trade_min = $trade_range[0];
            $trade_max = $trade_range[1];
            if($request->key == 'trade_min') {
                $trade_min = $request->value;
            }else if($request->key == 'trade_max'){
                $trade_max = $request->value;
            }

            $trade_range = implode(';', [$trade_min, $trade_max]);
            DB::table('settings')->where('setting_name', 'trade_range')->update([
                'setting_value' => $trade_range
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Cập nhật giới hạn giao dịch thành công.',
            ]);
        }
        
    }
}
