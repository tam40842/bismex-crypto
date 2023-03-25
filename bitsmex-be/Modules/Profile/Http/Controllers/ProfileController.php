<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\Vuta\Vuta;
use Hash;
use DB;
use Storage;
use Auth;
use Validator;
use App\Twofa;
use App\User;
use Twilio\Rest\Client;
use Modules\Profile\Entities\VerifyPhone;

class ProfileController extends Controller
{
    use ValidatesRequests, Vuta;

    public function postCerifyPhone(Request $request) {
        $this->validate($request, [
            'phone_number' => 'required'
        ]);
        $check_phone = User::where('phone_number', $request->phone_number)->first();
        if($check_phone) {
            return response()->json([
                'status' => 422,
                'message' => 'This phone number already exists.',
            ]);
        }
        $phone = $this->clean($request->phone_number);
        $phone = substr($phone, 1);
        $verify_phone_user = VerifyPhone::whereDate('created_at', now())->where('userid', Auth::id())->count('id');
        if($verify_phone_user >= 3) {
            return response()->json([
                'status' => 422,
                'message' => 'The limit of SMS Code is  3 times per day.'
            ]);
        }
        $account_sid = 'AC696d368686d8f7a327d1672552bba207';
        $auth_token = 'fb670977f5799ad8e5df67e35c661cef';
        $twilio_number = "+17028274111";

        $client = new Client($account_sid, $auth_token);
        $otp = random_int(000000, 999999);
        
        $client->messages->create(
            // Where to send a text message (your cell phone?)
            '+84'.$phone,
            array(
                'from' => $twilio_number,
                'body' => 'Your Bitsmex App verification code is '.$otp
            )
        );
        VerifyPhone::create([
            'userid' => Auth::id(),
            'code' => $otp,
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'successfully'
        ]);
    }

    public function postCerifyPhoneCode(Request $request) {
        $this->validate($request, [
            'phone_number' => 'required',
            'twofa_code' => 'required|numeric',
            'otp' => 'required|numeric'
        ]);
        $verify_phone_user = VerifyPhone::where('userid', Auth::id())->where('code', $request->otp)->first();
        if(is_null($verify_phone_user)) {
            return response()->json([
                'status' => 422,
                'message' => 'Your SMS Code is wrong! Please try again!'
            ]); 
        }
        $twofa = new Twofa();
        $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        if (!$valid) {
            return response()->json([
                'status' => 422,
                'message' => 'The Two-factor Authentication code is invalid.'
            ]); 
        }
        $user = User::find(Auth::id());
        $user->update([
            'phone_number' => $request->phone_number,
            'phone_status' => 1,
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'You have successfully connected your phone number to your account.'
        ]); 
    }

    public function getKycDocument() {
        $user = Auth::user();
        $kyc = DB::table('kycs')->where('kycs.userid', $user->id)->join('users', 'kycs.userid', '=', 'users.id')
        ->select('kycs.*', 'users.first_name', 'users.phone_number as phone_number', 'users.last_name', 'users.kyc_status', 'users.identity_number as passport', 'users.country')->first();

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => [
                'kyc' => !is_null($kyc) ? $kyc : []
            ]
        ]);
    }

    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
            // 'twofa_code' => 'required|numeric',
        ]);
        $user = Auth::user();
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        // $twofa = new Twofa();
        // $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        // if (!$valid) {
        //     return response()->json([
        //         'status' => 422,
        //         'message' => 'The Two-factor Authentication code is invalid.'
        //     ]); 
        // }
        if($user->google2fa_enable != 1) {
            return response()->json([
                'status' => 422,
                'message' => 'You have to submit TWO-FACTOR AUTHENTICATION to be fully functional.'
            ]); 
        }
        if(!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 422,
                'message' => 'The Current password does not match.',
            ], 200);
        }
        if(Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 422,
                'message' => 'The new password not matches the your current password.',
            ], 200);
        }
        $user->update([
            'password' => Hash::make($request->password),
            'updated_at' => date(now())
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Your Password has been updated.'
        ], 200);
    }

    public function ChangeImage(Request $request) {
        if($request->has('identity_frontend')) {
            $this->validate($request,
            [
                'identity_frontend' => 'mimes:jpeg,jpg,png,gif|max:20480'
            ],
            [
                'identity_frontend.image' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB',
                'identity_frontend.max' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB'
            ]);
            $file = $request->file('identity_frontend');
            $field = 'identity_frontend';
        }else if($request->has('identity_backend')) {
            $this->validate($request,
            [
                'identity_backend' => 'mimes:jpeg,jpg,png,gif|max:20480'
            ],
            [
                'identity_backend.image' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB',
                'identity_backend.max' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB'
            ]);
            $file = $request->file('identity_backend');
            $field = 'identity_backend';
        }else if($request->has('selfie')) {
            $this->validate($request,
            [
                'selfie' => 'mimes:jpeg,jpg,png,gif|max:20480'
            ],
            [
                'selfie.image' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB',
                'selfie.max' => 'Upload format only (jpeg, jpg, png, gif) and size less then 5MB'
            ]);
            $file = $request->file('selfie');
            $field = 'selfie';
        }else {
            return response()->json([
                'status' => 422,
                'message' => 'Access denied'
            ], 200);
        }
        $user = Auth::user();
        $img = Vuta::images($file);
        do{
            $kyc = DB::table('kycs')->where('userid', $user->id)->first();
            if(!$kyc) {
                DB::table('kycs')->insert([
                    'userid' => $user->id,
                    'created_at' => now(),
                ]);
            }else {
                DB::table('kycs')->where('userid', $user->id)->update([
                    $field => $img,
                ]);
            }
        }
        while(is_null($kyc));
        return response()->json([
            'status' => 200,
            'message' => 'Upload successfully',
            'data' => [
                'image' => $img
            ]
        ]);
    }

    public function postKyc(Request $request) {
        $this->validate($request,
        [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'country' => 'required',
            'phone_number' => 'required|regex:/(0)[0-9]{9}/',
            'passport' => 'required',
        ]);
        $user = Auth::user();
        if(in_array($user->kyc_status, [1, 2])) {
            return response()->json([
                'status' => 422,
                'message' => 'Access denied.',
            ], 200);
        }
        do{
            $kyc = DB::table('kycs')->where('userid', $user->id)->first();
            if(!$kyc) {
                DB::table('kycs')->insert([
                    'userid' => $user->id,
                    'created_at' => now(),
                ]);
            }else {
                DB::table('kycs')->where('userid', $user->id)->update([
                    'status' => 0,
                ]);
            }
        }
        while(is_null($kyc));

        if(!$kyc->identity_frontend || !$kyc->identity_backend) {
            return response()->json([
                'status' => 422,
                'message' => 'You do not have enough images to update this information yet.'
            ]);
        }
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'country' => $request->country,
            'phone_number' => $request->phone_number,
            'identity_number' => $request->passport,
            'kyc_status' => 1,
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Your document has been updated.',
        ]);
    }

    public function changeProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            // 'phone_number' => 'required|regex:/(0)[0-9]{9}/'
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date_format:Y-m-d|before:today|nullable'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'updated_at' => now()
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Your profile has been updated.'
        ]);
    }

    public function twofaSubmit(Request $request) {
        $validator = Validator::make($request->all(), ['twofa_code' => 'required|string|min:6|max:6']);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ]);
        }
        $user = Auth::user();
        $twofa = new Twofa();
        $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
        if(!$valid) {
            return response()->json([
                'status' => 422,
                'message' => 'The two factor code is invalid.'
            ]);
        }
        $user->google2fa_enable = $user->google2fa_enable ? 0 : 1;
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'The two factor authenticator has been updated.',
        ]);
    }

    public function hideBalance() {
        $user = Auth::user();
        $user->hide_balance = $user->hide_balance ? 0 : 1;
        $user->save();
        return response()->json([
            'status' => 200,
            'message' => 'Your balance has been hidden.',
        ]);
    }

    public function postUploadAvatar(Request $request) {
        $this->validate($request, ['avatar' => 'required|image']);
        $user = User::find(Auth::id());
        if($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $user->update([
                'avatar' => Vuta::images($file),
                'updated_at' => now()
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Update avatar Successfully.',
        ]);
    }

    public function getAPIKeys() {
        $user = Auth::user();
        $APIKeys = DB::table('api_token')->where('user_id', $user->id)->get();

        return response()->json([
            'status' => 200,
            'data' => $APIKeys,
        ]);
    }

    public function postAddAPIKeys() {
        $user = Auth::user();
        $secret_token = $this->generateRandomString();
        $token = Hash::make($secret_token);
        
        DB::table('api_token')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'secret_token' => $secret_token,
            'status' => 1,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Create API Key Successfully.',
        ]);
    }

    function generateRandomString($length = 50) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function postEditAPIKeys($id) {
        $APIKey = DB::table('api_token')->where('id', $id)->first();
        if(!$APIKey) {
            return response()->json([
                'status' => 422,
                'message' => 'API key not found',
            ]);
        }

        DB::table('api_token')->where('id', $id)->update([
            'status' => ($APIKey->status == 1) ? 0 : 1,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Update API Key Success',
        ]);
    }

    public function postDeleteAPIKeys($id) {
        $APIKey = DB::table('api_token')->where('id', $id)->first();
        if(!$APIKey) {
            return response()->json([
                'status' => 422,
                'message' => 'API key not found',
            ]);
        }

        DB::table('api_token')->where('id', $id)->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Delete API Key Success',
        ]);
    }
}
