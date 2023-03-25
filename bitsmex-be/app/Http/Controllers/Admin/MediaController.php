<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;
use DB;
use Auth;
use Illuminate\Support\Str;
use Gate;

class MediaController extends Controller
{
    public function __construct(){
        
    }

    function getMedia(){
        Gate::allows('modules', 'media_access');

        $data = DB::table('media')->orderBy('media_id', 'DESC')->offset(0)->limit(100)->get();
        $date_filter = DB::table('media')->distinct()->select('media_folder')->orderBy('media_folder', 'DESC')->get();
        return view('admin.media.index', ['data' => $data, 'date_filter' => $date_filter]);
    }

    function postMedia(Request $request){
        $this->validate($request, ['file' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',]);
        $file = $request->file('file');
        $file_size = $file->getSize();
        $date_folder = date('Y-m-d');
        $path = 'public/uploads/system/'.$date_folder;
        $extension = $file->getClientOriginalExtension();
        $mime_type = $file->getClientMimeType();
        $file_type = $this->get_media_type($extension);
        $slug_name = str_replace('.' . $extension, '', trim($file->getClientOriginalName()));
        $file_name = Str::slug($slug_name);
        $filename_origin = $file_name;
        
        $width = 0;
        $height = 0;
        $media_name = $file_name;
        if($file_type == 'image' || $file_type == 'icon'){
            list($width, $height) = getimagesize($file);
            if($width < $height){
                $media_style = 'portrait';
            }else{
                $media_style = 'landscape';
            }
            $file_name = $file_name . '_size_' . $width . 'x' . $height;
        }
        $get_file_name = DB::table('media')->where('media_name', $file_name)->count();
        $file_index = 2;
        while($get_file_name > 0){
            $file_name = $file_name . '-' . $file_index;
            $get_file_name = DB::table('media')->where('media_name', $file_name)->where('media_extension', $extension)->count();
            $file_index++;
        }
        $cdn_upload = Storage::disk('local')->putFileAs($path, $file, $file_name . '.' . $extension);
        $file_path = Storage::disk('local')->url($cdn_upload);
        $media_source = url($file_path);
        $media_style = 'landscape';
        $media_path = str_replace($file_name . '.' . $extension, '', $media_source);
        if($file_type == 'image' || $file_type == 'icon'){
            $media_url = $media_source;
            if($file_type == 'image'){
            }
        }else{
            $media_url = url('/contents/images/media_thumbs/' . $file_type . '.png');
        }
        $media_author = !Auth::guest() ? Auth::user()->id : 0;
        $media = [
            'media_name' => $file_name,
            'media_extension' => $extension,
            'media_width' => $width,
            'media_height' => $height,
            'media_style' => $media_style,
            'media_size' => $file_size,
            'mime_type' => $mime_type,
            'media_type' => $file_type,
            'media_source' => $media_source,
            'media_url' => $media_url,
            'media_alt' => $media_name,
            'media_description' => '',
            'media_path' => $media_path,
            'media_location' => $path,
            'media_folder' => $date_folder,
            'media_author' => $media_author,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $media_id = DB::table('media')->insertGetId($media);
        $media['media_id'] = $media_id;
        return $media;
    }

    function getMediaLazy(Request $request){
        $offset = $request->offset;
        $limit = $request->limit;
        $media_type = $request->media_type;
        $media_date = $request->media_date;
        $media_search = $request->media_search;
        $type_operator = '=';
        if($media_type == 'all'){
            $media_type = '';
            $type_operator = '!=';
        }
        $date_operator = '=';
        if($media_date == 'all'){
            $media_date = '';
            $date_operator = '!=';
        }
        $search_operator = 'like';
        if($media_search == ''){
            $search_operator = '!=';
        }
        // $data = DB::table('media')->where('media_type', $type_operator, $media_type)->where('media_folder', $date_operator, $media_date)->where('media_name', $search_operator, '%' . $media_search . '%')->orderBy('media_id', 'DESC')->offset($offset)->limit($limit)->get();
        $data = DB::table('media')->orderBy('media_id', 'DESC')->offset($offset)->limit($limit)->get();
        return response()->json($data);
    }

    function getMediaFilter(Request $request){
        $media_type = $request->media_type;
        $media_date = $request->media_date;
        $media_search = $request->media_search;
        $type_operator = '=';
        if($media_type == 'all'){
            $media_type = '';
            $type_operator = '!=';
        }
        $date_operator = '=';
        if($media_date == 'all'){
            $media_date = '';
            $date_operator = '!=';
        }
        $search_operator = 'like';
        if($media_search == ''){
            $search_operator = '!=';
        }
        $data = DB::table('media')->where('media_folder', $date_operator, $media_date)->orderBy('media_id', 'DESC')->offset(0)->limit(50)->get();
        return $data;
    }

    function getMediaAlone(Request $request){
        $media_id = $request->media_id;
        $data = DB::table('media')->where('media_id', $media_id)->first();
        return response()->json($data);
    }

    function postSaveMedia(Request $request){
        $media_id = $request->media_id;
        $media_alt = $request->media_alt;
        $media_description = $request->media_description;
        $media = ['media_alt' => $media_alt, 'media_description' => $media_description];
        DB::table('media')->where('media_id', $media_id)->update($media);
        return 'true';
    }

    function postDeleteMedia(Request $request){
        $media_id = $request->media_id;
        $media = DB::table('media')->where('media_id', $media_id)->first();
        if(is_null($media)) {
            return false;
        }
        Storage::disk('local')->delete(env('FTP_CDN_PATH').$media->media_location . '/' . $media->media_name . '.' . $media->media_extension);
        DB::table('media')->where('media_id', $media_id)->delete();
        return 'true';
    }

    function postDeleteMultiMedia(Request $request){
        $media_ids = $request->media_ids;
        if(count($media_ids) > 0){
            foreach($media_ids as $media_id){
                $media = DB::table('media')->where('media_id', $media_id)->first();
                if(is_null($media)) {
                    return false;
                }
                Storage::disk('local')->delete(env('FTP_CDN_PATH').$media->media_location . '/' . $media->media_name . '.' . $media->media_extension);
                DB::table('media')->where('media_id', $media_id)->delete();
            }
            return 'true';
        }else{
            return 'false';
        }
    }

    function get_media_type($type){
        $image = ['JPE','JPEG','JPG','PNG', 'GIF', 'SVG', 'ICO'];
        $icon = ['ICO'];
        $res = 'other';
        if(in_array(strtoupper($type), $icon)){
            $res = 'icon';
        }
        if(in_array(strtoupper($type), $image)){
            $res = 'image';
        }
        return $res;
    }
}