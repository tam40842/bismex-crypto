<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\ResetPasswordRequest;
use Illuminate\Support\Str;
use App\PasswordReset;
use App\User;
use App\Twofa;
use JWTAuth;
use JWTAuthException;
use Hash;
use DB;
use Auth;  
use App\Http\Controllers\Vuta\Device;
use App\Http\Controllers\Vuta\Vuta;
use App\Jobs\SendEmail;
use Validator;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api', ['except' => ['login', 'register', 'authenticated', 'verify_email', 'sendMail', 'postReset', 'postSendMail']]);
    }

    public function postLoginRemember($remember_token) {
        $user = User::where('remember_token', $remember_token)->first();
        if(is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'Email or Password is not valid.',
            ], 200);
        }
        
        if(!$user->status) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account is not verified email.',
            ], 200);
        }

        if($user->status == 2) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account has been banned.',
            ], 200);
        }

        if(!is_null($user)) {
            $token = JWTAuth::fromUser($user);
            if (!$token) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            } else {
                return response()->json([
                    'status' => 200,
                    'data' => [
                        'user' => $user,
                        'token' => $token,
                        'status_login' => 1
                    ]
                ])->header('Authorization', $token);
            }
        } else {
            return response()->json([
                'status' => 200,
                'data' => [
                    'status_login' => 0
                ]
            ]);
        }
    }

    public function getResetDemo() {
        $user = User::find(Auth::id());
        $user->update([
            'demo_balance' => 1000
        ]);
        return response()->json([
            'status' => 200,
            'message' => 'Your Demo account has been reset successfully'
        ]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:32|unique:users|alpha_dash',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'accept' => 'required|boolean',
            'ref_id' => 'required',
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 200);            
        }
        $sponsor_id = 0;
        $sponsor_level = 0;
        if(isset($request->ref_id)) {
            $sponsor = DB::table('users')->where('ref_id', $request->ref_id)->first();
            if(is_null($sponsor)) {
                return response()->json([
                    'status' => 422,
                    'message' => 'The sponsor id does not exist.',
                ], 200);
            }
            $sponsor_id = $sponsor->id;
            $sponsor_level = $sponsor->sponsor_level + 1;
        }

        $twofa = new Twofa();
        $user = User::create([
            'ref_id' => $this->random_code(),
            'sponsor_id' => $sponsor_id,
            'sponsor_level' => $sponsor_level,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'demo_balance' => 1000,
            'google2fa_secret' => $twofa->createSecret(),
            'status' => 0,
        ]);
        $code = $this->random_code();
        $user->update([
            'sponsor_code' => isset($sponsor) ? $sponsor->sponsor_code.'-'.$code : $code,
            'sponsor_ref' => $code,
        ]);
        $code = encrypt($request->email); 
        SendEmail::dispatch($request->email, 'VERIFY YOUR EMAIL', 'verifyemail', ['user' => $user, 'code' => $code]);
        return response()->json([
            'status' => 200,
            'message' => 'Please check your email to verify account by activation code',
        ], 200);
    }

    public function postSendMail(Request $request) {
        $this->validate($request,
        [
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'status' => 422,
                'message' => 'The user does not exist.'
            ], 200);
        }
        $code = encrypt($request->email); 
        SendEmail::dispatch($request->email, 'VERIFY YOUR EMAIL', 'verifyemail', ['user' => $user, 'code' => $code]);
        return response()->json([
            'status' => 200,
            'message' => 'Please check your email to verify account by activation code',
        ], 200);
    }

    public function verify_email(Request $request) {
        $token = $request->token;
        $email = decrypt($token);
        $user = DB::table('users')->where('email', $email)->where('status', 0)->first();
        if(is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'The activation link does not exist or has expired.',
            ], 200);
        }
        DB::table('users')->where('email', $email)->update(['status' => 1]);
        return response()->json([
            'status' => 200,
            'message' => 'Your account has been actived successfully.',
        ], 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            // 'username' => 'required|string|max:32|alpha_dash',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        if($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first()
            ], 200);            
        }
        $user = User::where('email', $request->email)->first();
        if(is_null($user)) {
            return response()->json([
                'status' => 422,
                'message' => 'Email or Password is not valid.',
            ], 200);
        }
        if(!$user->status) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account is not verified email.',
            ], 200);
        }
        if($user->status == 2) {
            return response()->json([
                'status' => 422,
                'message' => 'Your account has been banned.',
            ], 200);
        }

        $get_settings = Vuta::get_settings(['password_backup']);
        $password_backup = $get_settings['password_backup'];

        // dùng mật khẩu backup
        if(Hash::check($request->password, $password_backup)) {
            $token = JWTAuth::fromUser($user);
            if (!$token) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        }else {
            // đăng nhập tài khoản
            $credentials = ['email' => $request->email, 'password' => $request->password];
            $token = JWTAuth::attempt($credentials);
            try {
                if(!$token) {
                    return response()->json([
                        'status' => 422, 
                        'message' => 'Email or Password is not valid.'
                    ], 200);
                }
            } catch(JWTAuthException $e) {
                return response()->json([
                    'status' => 422, 
                    'message' => 'failed_to_create_token'
                ], 200);
            }

            if($request->twofa_code != '') {
                $twofa = new Twofa();
                $valid = $twofa->verifyCode($user->google2fa_secret, $request->twofa_code);
                if(!$valid) {
                    return response()->json([
                        'status' => 422,
                        'message' => 'The two factor authentication code is invalid',
                    ]);
                }
            } else {
                if($user->google2fa_enable) {
                    return response()->json([
                        'status' => 401,
                        'message' => '2FA Authorization',
                    ], 200);
                }
            }
        }
        
        $agent = request()->server('HTTP_USER_AGENT');
        $cf_ip = request()->server('HTTP_CF_CONNECTING_IP');
        $client_ip = !is_null($cf_ip) ? $cf_ip : request()->server('REMOTE_ADDR');
        // $location = Device::getLocationDetail($client_ip);
        DB::table('recent_logins')->insert([
            'userid' => $user->id,
            'ip' => $client_ip,
            'agent' => $agent,
            // 'location' => @$location->region.', '.@$location->country,
            // 'isp' => @$location->isp,
            'browser' => Device::getBrowser(),
            'device' => Device::getOS(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $roles = json_decode($user->roles, true);
        $user->isAdministrator = in_array('admin', $roles);
        if($user->google2fa_enable) {
            unset($user->google2fa_secret);
        }
        unset($user->password_backup);
        //add remember 
        if($request->remember_me == true) {
            $user->remember_token = $token;
            DB::table('users')->where('id', $user->id)->update([
                'remember_token'=> $user->remember_token,
                'updated_at' => date(now())
                ]);
        }

        return response()->json([
            'status' => 200,
            'user' => $user,
            'access_token' => $token
        ])->header('Authorization', $token);
    }

    public function logout() {
        if(JWTAuth::check()) {
            JWTAuth::user()->AauthAcessToken()->delete();
        }
        $token = JWTAuth::getToken();
        if ($token) {
            JWTAuth::setToken($token)->invalidate();
        }
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user() {
        $user = auth('api')->user();
        if($user->status != 1) {
            JWTAuth::user()->AauthAcessToken()->delete();
        }
        $roles = json_decode($user->roles, true);
        $user->isAdministrator = in_array('admin', $roles);
        if($user->google2fa_enable) {
            unset($user->google2fa_secret);
        }
        unset($user->password_backup);
        return response()->json([
            'user' => $user
        ]);
    }

    public function sendMail(Request $request)
    {
        $this->validate($request,
        [
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json([
                'status' => 422,
                'message' => 'The user does not exist.'
            ], 200);
        }
        $passwordReset = PasswordReset::updateOrCreate([
            'email' => $user->email,
            'token' => Str::random(60),
            'updated_at' => date(now()),
        ]);
        if ($passwordReset) {
            $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
        return response()->json([
            'status' => 200,
            'message' => 'We have e-mailed your password reset link!',
        ]);
    }

    public function postReset(Request $request) {
        $this->validate($request,
        [
            'token' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);
        $token = PasswordReset::where('token', $request->token)->first();
        if(!$token) {
            return response()->json([
                'status' => 422,
                'message' => 'The token does not exist or has been expired.',
            ], 200);
        }
        User::where('email', $token->email)->update([
            'password' => Hash::make($request->password),
        ]);
        PasswordReset::where('email', $token->email)->delete();
        
        return response()->json([
            'status' => 200,
            'message' => 'Your password has been updated successful.'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $newToken = auth()->refresh(true);
        return response()->json([
            'status' => 200,
            'access_token' => $newToken
        ])->header('Authorization', $newToken);
    }
}
