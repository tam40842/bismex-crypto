<?php
namespace App\Http\Controllers\Vuta;

use DB;
use App\Commission;
use App\User;
use App\Currencies;
use Storage;
use Str;

trait Vuta {
	public static function random_code() {
		//uniqid random integer (1,100) limit 6;
		return substr(md5(uniqid(rand(1,100))), 0, 8);
	}

	public static function Permissions($user_id) {
		$user = User::findOrFail($user_id);
		if($user->permission == null) {
			return abort(404);
		}
		$role = DB::table('roles')->where('slug', $user->permission)->first();
		if(!is_null($role)) {
			$modules = config('admin_menu');
			foreach($modules as $key => $value) {
				if(isset($value['sub'])) {
					foreach($value['sub'] as $module_sub){
						$module = DB::table('permissions')->where('id_role', $role->id)->where('slug_module', $module_sub['name'])->first();
						if($module) {
							return redirect($module_sub['url']);
						} 
					}
				}else {
					$module = DB::table('permissions')->where('id_role', $role->id)->where('slug_module', $value['name'])->first();
					if($module) {
						return redirect($value['url']);
					}
				}
			}
			return abort(404);
		}
		return abort(404);
	}

	public static function images($images) {
		$file = $images;
        $file_size = $file->getSize();
        $date_folder = date('Y-m-d');
        $path = 'public/uploads/avatar/'.$date_folder;
        $extension = $file->getClientOriginalExtension();
        $mime_type = $file->getClientMimeType();
        $slug_name = str_replace('.' . $extension, '', trim($file->getClientOriginalName()));
        $file_name = Str::slug($slug_name);
        $filename_origin = $file_name;
        
        $width = 0;
        $height = 0;
        $media_name = $file_name;
        $get_file_name = DB::table('media')->where('media_name', $file_name)->count();
        $file_index = 2;
        while($get_file_name > 0){
            $file_name = $file_name . '-' . $file_index;
            $get_file_name = DB::table('media')->where('media_name', $file_name)->where('media_extension', $extension)->count();
            $file_index++;
        }
        $cdn_upload = Storage::disk('local')->putFileAs($path, $file, $file_name . '.' . $extension);
        $file_path = Storage::disk('local')->url($cdn_upload);
		return $file_path;
	}

	//save image in vps
	public static function imagesV2($images) {
		$new_name_image = rand(1,999999) . '-' . $images->getClientOriginalName();
		$dir = 'avatar/'.date('Y-m-d');
		$storedPath = $images->move('images', $new_name_image);
		return $storedPath;
	}

	// fix 29/02/2020
	public static function validate_username($username) {
		// bỏ khoảng trống. Username ko được có khoản trống
		$username = str_replace(array(chr(32)), '', $username);
		
		// return
		return $username;
	}
	// fix 29/02/2020
    public static function get_settings($args) {
		$data = [];
		if(!is_null($args)){
			foreach($args as $value){
				$data[$value] = '';
			}
			$settings = DB::table('settings')->whereIn('setting_name', $args)->select('setting_name', 'setting_value')->get();
			if(!is_null($settings)){
				foreach($settings as $setting){
					$data[$setting->setting_name] = $setting->setting_value;
				}
			}
		}
		return $data;
	}

	public static function clean($string) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	 
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	 }
	
	public static function time_to_date($time, $type = ""){
        $strreturn = "";
        switch($type) {
            case "H:i, d/m/Y":
            case "d/m/Y":
                $strreturn = date($type,$time);
                break;
            case "abbr":
                $strreturn = '<abbr class="DateTime" data-time="'.$time.'" data-diff="'.(time()-$time).'" data-datestring="'.date("d/m/Y",$time).'" data-timestring="'.date("H:i",$time).'"></abbr>';
                break;
            default:
                if (($remain=time()-$time)>= 86400 * 30)
                    $strreturn = date("H:i, d-m-Y",$time);
                else
                {
                    if($remain >= 86400)
                        $strreturn = intval($remain/86400)." ngày trước";
                    elseif ($remain>=3600)
                        $strreturn = intval($remain/3600)." giờ trước";
                    elseif ($remain>=60)
                        $strreturn = intval($remain/60)." phút trước";
                    else
                        $strreturn = $remain." giây trước";
                }
                break;
        }
        return $strreturn;
	}

	public static function media($media_source, $thumb = false){
        $media = DB::table('media')->where('media_source', $media_source)->first();
		$data = $media->media_url;
		if(!is_null($media)){
			if($media->media_type == 'image'){
				if($thumb == true){
					$data = str_replace('_size_' . $media->media_width . 'x' . $media->media_height . '.' . $media->media_extension, '_thumb.' . $media->media_extension, $media->media_url);
				}else{
					$data = $media->media_url;
				}
			}
		}
		return $data;
	}

	public static function get_currencies() {
		$currencies = DB::table('currencies')->where('actived', 1)->get();
		return $currencies;
	}

	public function asset_logs($table, $data) {
		$to_symbol = in_array($data->action, ['BUY', 'SELL']) ? 'VND' : null;
		$amount = $data->amount;
		if(($table == 'debit_histories' && $data->action == 'BUY') || ($table == 'credit_histories' && $data->action == 'SELL')) {
			$amount = $data->total;
		}
		DB::table($table)->insert([
			'action' => $data->action,
			'userid' => $data->userid,
			'from_symbol' => $data->symbol,
			'to_symbol' => $to_symbol,
			'amount' => $amount,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);
	}

	protected function get_menu_items($menu_id){
		$items = DB::table('menu_items')->where('menu_id', $menu_id)->orderBy('sort_order')->get();
		if(count($items) > 0){
			$data = $this->menu_recursive($items, 1);
			return $data;
		}else{
			return null;
		}
    }
    
    protected function menu_recursive($items, $level, $menu_parent = 0){
        $result = '';
        foreach ($items as $item){
            if ($item->menu_item_parent == $menu_parent) {
            	$blank_checked = $item->menu_item_blank == 1 ? ' checked' : '';
				$result .= '<li class="dd-item" data-id="' . $item->menu_item_id . '">
					<div class="dd-handle">
						<span class="drag-indicator"></span>
						<div> ' . $item->menu_item_name . ' </div>
						<div class="dd-nodrag btn-group ml-auto ">
						<button class="btn btn-sm btn-secondary open_menu_modify text-primary"><i class="fa fa-pencil"></i></button> 
						<a class="btn btn-sm btn-secondary remove_menu_item text-danger" href="javascript:void(0);" onclick="event.preventDefault();"><i class="fa fa-trash"></i></a>
						</div>
					</div>

                    <div class="modify_menu_item p-3" style="display: none;">
                    	<div class="form-group">
                    		<label><i>Tên menu</i></label>
                            <div class="form-line">
                                <input type="text" name="menu_item_name[' . $item->menu_item_id . ']" class="form-control form-control-sm" placeholder="Tên menu" value="' . $item->menu_item_name . '">
                            </div>
                    	</div>
                    	<div class="form-group">
                    		<label><i>Liên kết đến menu</i></label>
                            <div class="form-line">
                    		    <input type="text" name="menu_item_link[' . $item->menu_item_id . ']" class="form-control form-control-sm" placeholder="Liên kết đến menu" value="' . $item->menu_item_link . '">
                            </div>
                    	</div>
                    	<hr>
                    	<p class="text-right">
							<a class="close_menu_modify" href="javascript:void(0);" onclick="event.preventDefault();"><i  class="fa fa-check fa-fw text-success"></i> Done</a>
						</p>
                    </div>' . self::menu_recursive($items, $level+1, $item->menu_item_id) . '
                </li>';
            }
        }
        return $result ? '<ol class="dd-list border border-secondary">' . $result . '</ol>' : '';
	}
	
	protected function update_menu_item_sort_order($menu_item_id, $sort_order){
		$check = DB::table('menu_items')->where('menu_item_id', $menu_item_id)->count();
		if($check > 0){
    		DB::table('menu_items')->where('menu_item_id', $menu_item_id)->update(['sort_order' => $sort_order]);
    	}
	}
	
	protected function reset_menu_items($menu_id, $not_in_id_array){
		DB::table('menu_items')->where('menu_id', $menu_id)->whereNotIn('menu_item_id', $not_in_id_array)->delete();
	}
	
	protected function update_menu_item($menu_item_id, $menu_item){
    	$check = DB::table('menu_items')->where('menu_item_id', $menu_item_id)->count();
    	if($check > 0){
    		DB::table('menu_items')->where('menu_item_id', $menu_item_id)->update($menu_item);
    	}
	}
	
	protected function add_menu_item($menu_item){
    	$menu_item_id = DB::table('menu_items')->insertGetId($menu_item);
    	return $menu_item_id;
	}
	
	public static function menu($menu_location = '', $menu_class = ''){
		if($menu_location == ''){
			return '';
		}else{
			$get_menu_id = DB::table('menus')->where('menu_location', 'like', '%"' . $menu_location . '"%')->select('menu_id')->first();
			if(!is_null($get_menu_id)){
				$menu_id = $get_menu_id->menu_id;
				$menu_items = DB::table('menu_items')->where('menu_id', $menu_id)->orderBy('sort_order')->get();
				$data = self::menu_item($menu_items, 1, 0, $menu_location . ' menu_' . $menu_id . ' ' . $menu_class);
				return $data;
			}else{
				return '';
			}
		}
	}

	public static function menu_item($items, $level, $menu_parent = 0, $menu_class_name = ''){
		$html = '';
		$ul_attributes = ($menu_parent > 0) ? '' : 'class="'.$menu_class_name.'"';
		$list_parent = [];
		foreach($items as $key => $value) {
			$list_parent[] = ($value->menu_item_parent > 0) ? $value->menu_item_parent : '';
		}
		$list_parent = array_unique(array_filter($list_parent));
		foreach($items as $key => $value) {
			if($menu_parent == $value->menu_item_parent) {
				$li_dropdown = '';
				$icon = '';
				if(in_array($value->menu_item_id, $list_parent)) {
					$li_dropdown = $menu_parent == 0 ? 'dropdown' : '';
					$icon = '<i class="fa fa-angle-down" aria-hidden="true"></i>';
				}
				if($menu_parent > 0) {
					$li_dropdown = 'dropdown-item';
				}
				$a_dropdown = in_array($value->menu_item_id, $list_parent) ? ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"' : '';
				$html .= '<li class="'.$li_dropdown.'">';
				$html .= '<a href="'.$value->menu_item_link.'" '.$a_dropdown.'>';
				$html .= '<span>'.$value->menu_item_name.'</span> '.$icon;
				$html .= '</a>';
				$html .= self::menu_item($items, $level, $value->menu_item_id, '');
				$html .= '</li>';
			}
		}
		return '<ul '.$ul_attributes.'>'.$html.'</ul>';
	}

	public function checkSlug_exists($table, $title, $id = 0) {
        $slug = str_slug(trim($title));
        $index = 2;
        $new_slug = $slug;
        $check = DB::table($table)->where('slug', $new_slug);
        if($id > 0) {
            $check = $check->where('id', '<>', $id);
        }
        $check = $check->exists();
        while($check){
            $new_slug = $slug . '-' . $index;
            $index++;
            $check = DB::table($table)->where('slug', $new_slug);
            if($id > 0) {
                $check = $check->where('id', '<>', $id);
            }
            $check = $check->exists();
        }
        return $new_slug;
	}
	
	public static function _substr($str, $length, $minword = 3){
		$sub = '';
		$len = 0;
		foreach (explode(' ', $str) as $word){
			$part = (($sub != '') ? ' ' : '') . $word;
			$sub .= $part;
			$len += strlen($part);
			if (strlen($word) > $minword && strlen($sub) >= $length){
				break;
			}
		}
		return $sub . (($len < strlen($str)) ? ' ...' : '');
	}

	protected function add_commission($userid, $total, $orderid) {
		$user = User::find($userid);
		if(is_null($user)) {
			return false;
		}
		$parent = User::find($user->sponsor_id);
		if(is_null($parent)) {
			return false;
		}
		$settings = self::get_settings(['commission']);
		$commission_percent = ($parent->ref_commission > 0) ? $parent->ref_commission : $settings['commission'];
		$commission = $total * $commission_percent / 100;
		$commission_data = Commission::create([
			'userid' => $parent->id,
			'orderid' => $orderid,
			'amount' => $commission
		]);
		DB::table('credit_histories')->insert([
			'action' => 'COMMISSION',
			'userid' => $parent->id,
			'to_symbol' => 'VND',
			'amount' => $commission,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);
		DB::table('user_balance')->where('userid', $parent->id)->increment('VND', $commission);
	}

	public static function estimate($userid) {
		$user = User::find($userid);
		$currencies = Currencies::where('actived', 1)->get();
		$balance = $user->UserBalance();
		$estimate = 0;
		foreach($currencies as $key => $value) {
			$good_price = DB::table('offers')->where('action', 'SELL')->where('symbol', $value->symbol)->orderBy('price', 'asc')->where('status', 0)->value('price_has_fee');
			$estimate += $balance->{$value->symbol} * $good_price;
		}
		return number_format($estimate + $balance->VND);
	}



    public static function get_sponsorTree($userid, $level = 0){
		$users = DB::table('users')->where('sponsor_id', $userid)->where('status', '1')->get();
		$level = $level + 1;
		$data = [];
		foreach($users as $key => $value) {
            $deposit_total = DB::table('deposit')->where('userid', $value->id)->sum('total');
            $volume_total = DB::table('orders')->where('userid', $value->id)->where('type', 'live')->sum('amount');
			$data[] = [
				"name" => $value->first_name.' '.$value->last_name,
				"avatar" => $value->avatar,
				"username" => $value->username,
				"email" => $value->email,
				"ref_id" => strtoupper($value->ref_id),
				"deposit_total" => $deposit_total,
				"volume_total" => $volume_total,
				"created_at" => $value->created_at,
				"level" => $level,
				"children" => self::get_sponsorTree($value->id, $level)
			];
		}
		return $data;
    }
}