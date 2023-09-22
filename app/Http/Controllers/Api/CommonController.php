<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Mail;
use Event;
use Illuminate\Support\Arr;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Stripe\Charge;
use Stripe\Customer;
use App\Models\User;
use App\Models\Setting;
use App\Models\Cms;
use App\Models\Conversions;
use App\Models\CardDetails;
use App\Models\Diamond;
use App\Models\Notifications;
use App\Models\LiveNotification;
use App\Models\LiveData;
use App\Models\Videos;
use App\Models\HostLiveNotification;
use App\Models\Otp;
use App\Models\Banner;
use Response;
use App\Models\PaymentHistory;
use App\Models\FollowUser;
use App\Helpers\Helper;
use App\Models\BlockUserList;
Use App\Models\Gift;
use Illuminate\Support\Facades\Log;

class CommonController extends Controller{
    
    // public function __construct(Request $request){

    //     $appbuild = getallheaders();
    //     $usertype = getallheaders();
    //     $app_version =  $appbuild['app_version'];
    //     $device_type =  $usertype['device_type'];
        
    //     if($device_type == 'ios'){
    //         $iosuser = Setting::where('code','iosversion')->first();
    //         if($iosuser->value <= $app_version){
    //             return response()->json(['status'  => 'success','message' => 'Success']);
    //         }else{
    //             http_response_code(404);
    //             $response['status'] = 'fail';
    //             $response['message'] = '\n Update required!!! \n\n A new version of this app is available. Please update it.\n\n WongaLive.';
    //             echo json_encode($response);
    //             exit;
    //         }
    //     }elseif($device_type == 'android'){
    //         $androiduser = Setting::where('code','androidversion')->first();
    //         if($androiduser->value <= $app_version){
    //             return response()->json(['status'  => 'success','message' => 'Success']);
    //         }else{
    //             http_response_code(404);
    //             $response['status'] = 'fail';
    //             $response['message'] = '\n Update required!!! \n\n A new version of this app is available. Please update it.\n\n WongaLive.';
    //             echo json_encode($response);
    //             exit;
    //         }
    //     }
    // }

        public function __construct(){
        // $a = getallheaders();
        // $b = getallheaders();
        // $c = getallheaders();
        // $app_version =  $a['app_version'];
        // $device_type =  $b['device_type'];
        // $app_type =  $c['app_type'];
        
        $new = getallheaders();

        $a = getallheaders();
        $b = getallheaders();
        $c = getallheaders();
        $app_version =  $a['app_version'];
        $device_type =  $b['device_type'];

        if (array_key_exists("app_type",$new))
            {
                $app_type =  $c['app_type'];
            }
            else
            {
                $app_type = "";
            }
        
        if($device_type == 'ios'){

            $iosuserapp = Setting::where('code','iostype')->first();
            if($iosuserapp->value == $app_type){
                $iosuser = Setting::where('code','iosversionpk')->first();
                if($iosuser->value <= $app_version){
                    return response()->json(['status'  => 'success','message' => 'Success']);
                }else{
                    http_response_code(404);
                    $response['status'] = 'fail';
                    $response['message'] = '\n Update required!!! \n\n A new version of this app is available. Please update it.\n\n WongaLive.';
                    echo json_encode($response);
                    exit;
                }
            }


            $iosuser = Setting::where('code','iosversion')->first();
            if($iosuser->value <= $app_version){
                return response()->json(['status'  => 'success','message' => 'Success']);
            }else{
                http_response_code(404);
                $response['status'] = 'fail';
                $response['message'] = '\n Update required!!! \n\n A new version of this app is available. Please update it.\n\n wongaLive.';
                echo json_encode($response);
                exit;
            }
        }elseif($device_type == 'android'){

            $androiduserapp = Setting::where('code','androidtype')->first();
            if($androiduserapp->value == $app_type){
                $iosuser = Setting::where('code','androidversionpk')->first();
                if($iosuser->value <= $app_version){
                    return response()->json(['status'  => 'success','message' => 'Success']);
                }else{
                    http_response_code(404);
                    $response['status'] = 'fail';
                    $response['message'] = '\n Update required!!! \n\n A new version of this app is available. Please update it.\n\n WongaLive.';
                    echo json_encode($response);
                    exit;
                }
            }

            $androiduser = Setting::where('code','androidversion')->first();
            if($androiduser->value <= $app_version){
                return response()->json(['status'  => 'success','message' => 'Success']);
            }else{
                http_response_code(404);
                $response['status'] = 'fail';
                $response['message'] = '\n Update required!!! \n\n A new version of this app is available. Please update it.\n\n wongaLive.';
                echo json_encode($response);
                exit;
            }
        }


    }
    
    public function getAuthenticatedUser(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
            return response()->json(['token_session'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for CMS Page
    -------------------------------------------------------------------------------------------- */

    public function CmsPage(Request $request){
        try{
            $allcms = json_decode(strip_tags(Cms::where('status','active')->get()),true);

            Log::info(json_encode($allcms));
            return response()->json(['status' => 'success','message' =>'All CMS Pages Get Successfully Done..!','data' => $allcms]);
        }catch(Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Update Token
    -------------------------------------------------------------------------------------------- */
    public function updateToken(Request $request){
        $token = $request->header('Authorization');
        try {
        $user = JWTAuth::parseToken()->authenticate();
        $userId = $user->id;
        $token = JWTAuth::refresh(str_replace('Bearer ',"",$token));
        } catch (Exception $e) {
        if($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
        return response()->json(['status' => "error",'code'=>500,'message' => 'Token is Invalid']);
        }else if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
        $token = JWTAuth::refresh(str_replace('Bearer ',"",$token));

        Log::info(json_encode($token));
        return response()->json(['status' => "error",'code'=>$e->getCode(),'message' => 'Token is Expired','token'=>$token]);
        }
        } catch(\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
        return response()->json(['status' => "error",'code'=>500,'message' => 'Token is Invalid']);
        
        } catch(\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e){
        return response()->json(['status' => "error",'code'=>500,'message' => 'Token is Blacklisted']);

        } catch(\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
        $token = JWTAuth::refresh(str_replace('Bearer ',"",$token));

        Log::info(json_encode($token));
        return response()->json(['status' => "error",'code'=>$e->getCode(),'message' => 'Token is Expired','token'=>$token]);
        } catch(JWTAuthException $e){
        return response()->json(['status' => "error",'code'=>$e->getCode(),'message' => $e->getMessage()]);
        }
        Log::info(json_encode($token));
        return response()->json(['status' => "success",'message' =>"Token Success",'token'=>str_replace('Bearer ',"",$token)]);
        }
    /* -----------------------------------------------------------------------------------------
    @Description: Function for Forgot Password
    -------------------------------------------------------------------------------------------- */

    public function forgotPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'type' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(['status'   => 'error','message'  => $validator->messages()->first()]);
        }
        try{
            $user = User::where(['email'=>$request->email,'user_type'=>$request->type])->first();
            if(!$user){
                return response()->json(['status'    => 'error','message'   => "Please Correct E-mail Address..!"]);
            } else {
                $password = Str::random(8);
                $mailData['mail_to']   = $user->email;
                $mailData['to_name']   = $user->first_name;
                $mailData['mail_from']   = 'admin@admin.com';
                $mailData['from_title']  = 'Reset Password';
                $mailData['subject']     = 'Reset Password';
                $data = [
                    'data' => $mailData,
                    'username'=>$user->first_name,
                    'password'=>$password
                ];
                Mail::send('emails.verify', $data, function($message) use($mailData) {
                    $message->subject($mailData['subject']);
                    $message->from($mailData['mail_from'],$mailData['from_title']);
                    $message->to($mailData['mail_to'],$mailData['to_name']);
                });
                if(Mail::failures()) {
                    return response()->json(['status'=>'error','message'=>'Mail failed']);
                }
                $user->password = \Hash::make($password);
                $user->link_code = \Hash::make($password);
                $user->save();
                return response()->json(['status'    => 'success','message'   => 'New password has been sent to your registered email address',]);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Reset Passeord
    -------------------------------------------------------------------------------------------- */

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email'  => 'required|string|email',
            'password' => 'required|string|min:8',
        ]);
        if($validator->fails()) {
            return response()->json(['status'   => 'error','message'  => $validator->messages()->first()]);
        }
        try{
            $user = User::where('email', $request->email)->where(function ($query) {
                $query->where('user_type','user');
                $query->where('status','active');
            })->first();
            if(!$user){
                return response()->json(['status'    => 'error','message'   => "Please Correct E-mail Address..!"]);
            }else{
                $today = Carbon::now();
                $linkEx_time = Carbon::parse($user->link_expire);
                if($today >= $linkEx_time){
                    return response(['status'    => 'error','message'   =>  'Your link is expired. please try again & generate new link.']);
                }else{
                    if(request('password') == $user->link_code){
                        $user->password = bcrypt(request('password'));
                        $user->save();
                        return response()->json(['status'    => 'success','message'   => 'Your Password Changed Successfully..!',]);
                    }else{
                        return response()->json(['status'    => 'error','message'   => 'Your Password Does not Match',]);
                    }
                }
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Home Page
    -------------------------------------------------------------------------------------------- */
    public function homescreen(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $checkblockuser = Helper::CheckBlockUser($user);
            $sendcheckblockuser = Helper::SendCheckBlockUser($user);
            User::where('id',$user->id)->update(['available_flag' => 'online']);
            Notifications::where('user_id',$user->id)->update(['flag_status' => "offline"]);
            LiveNotification::where('user_id',$user->id)->update(['status' => "offline"]);
            
            // LiveNotification::where('follow_user',$user->id)->update(['status' => "offline"]);
            
            //Popular User Array

            // $liveviewcount = LiveNotification::where('user_id','!=',$user->id)->where('live_no', ['1'])->count();

            $checklive = LiveNotification::where('user_id','!=',$user->id)->orderBy('id','desc')->groupby('user_id')->pluck('user_id');
            
            $popular_user = User::with(['follow_unfollow_flag' => function($query) use($user){
                $query->where('followed_user_id', $user->id);
                }])->whereIn('id',$checklive)->where('user_type','!=',$user->id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->groupBy('id')->get()->toArray();
            // dd($popular_user);
                foreach ($popular_user as $key => $value) {
                    $checkUserLive = LiveNotification::where('user_id', $value['id'])->whereIn('status', ['pending','online'])->orderBy('id','desc')->first();
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    // $check_live_count = LiveNotification::where('user_id',$value['id'])->sum('live_no');
                    $check_live_count = LiveNotification::where('user_id', $value['id'])->whereIn('viewer', ['1'])->count();
                    $liveviewcount = LiveNotification::where('user_id', $value['id'])->whereIn('viewer', ['1'])->count();
                    $popularset = Setting::where('code','popularset')->first();

                    $total_live_count = LiveNotification::where('user_id', $value['id'])->where('status', "online")->count();

                    if(!empty($checkUserLive->u_id)){
                        $popular_user[$key]['live_streaming_flge'] = '1';
                        $popular_user[$key]['channel_id'] = $checkUserLive->u_id;
                        $popular_user[$key]['viewer_count'] = (string)$liveviewcount;
                        $popular_user[$key]['total_live_count'] = (string)$total_live_count;
                    } else {
                        $popular_user[$key]['live_streaming_flge'] = '0';
                        $popular_user[$key]['channel_id'] = '0';
                        $popular_user[$key]['viewer_count'] = (string)$liveviewcount;
                        $popular_user[$key]['total_live_count'] = (string)$total_live_count;
                    }
                    // $user['diamond'] = $popularset->value;
                    if($check_live_count >= $popularset->value){
                        $popular_user[$key]['Popular_Count'] = $check_live_count;
                        $popular_user[$key]['Popular_Tag'] = "Popular User";
                    }else{
                        $popular_user[$key]['Popular_Count'] = $check_live_count;
                        $popular_user[$key]['Popular_Tag'] = "No Popular User";
                    }

                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $popular_user[$key]['blockflag'] = '1';
                    } else {
                        $popular_user[$key]['blockflag'] = '0';
                    }

                    if (!empty($value['follow_unfollow_flag'])) {
                        $popular_user[$key]['follow_flge'] = '1';
                    } else {
                        $popular_user[$key]['follow_flge'] = '0';
                    }

                    unset($popular_user[$key]['follow_unfollow_flag']);
                    unset($popular_user[$key]['live_streaming_flag']);
                    
                }

                if(sizeof($popular_user) > 0){
                    $data1['Popular'] = $popular_user;
                } else {
                    $data1['Popular'] = array();
                }
                // die();
            
                // Live User Array

                $checkUserLive = LiveNotification::where('user_id','!=',$user->id)->whereIn('status', ['pending','online'])->groupBy('user_id')->pluck('user_id');
                $user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                    $query->where('followed_user_id', $user->id);
                }])->whereIn('id',$checkUserLive)->where('user_type','!=',$user->id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('id','ASC')->get()->toArray();
                $msg = 'You Home Screen...!';
                $msg1 = 'Sorry, No one hosting the live streaming at this moment..!';
                foreach ($user_data as $key => $value) {
                    $checkUserLive = LiveNotification::where('user_id', $value['id'])->whereIn('status', ['pending','online'])->orderBy('id','desc')->first();
                    $liveviewcount = LiveNotification::where('user_id', $value['id'])->whereIn('viewer', ['1'])->count();
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    $check_live_count = LiveNotification::where('user_id',$value['id'])->sum('live_no');
                    
                    $total_live_count = LiveNotification::where('user_id', $value['id'])->where('status', "online")->count();
                    
                    if(!empty($checkUserLive->u_id)){
                        $user_data[$key]['live_streaming_flge'] = '1';
                        $user_data[$key]['channel_id'] = $checkUserLive->u_id;
                        $user_data[$key]['viewer_count'] = (string)$liveviewcount;
                        $user_data[$key]['total_live_count'] = (string)$total_live_count;
                    } else {
                        $user_data[$key]['live_streaming_flge'] = '0';
                        $user_data[$key]['channel_id'] = '0';
                        $user_data[$key]['viewer_count'] = (string)$liveviewcount;
                        $user_data[$key]['total_live_count'] = (string)$total_live_count;
                    }
                    
                    if($check_live_count >= $popularset->value){
                        $user_data[$key]['Popular_Count'] = $check_live_count;
                        $user_data[$key]['Popular_Tag'] = "Popular User";
                    }else{
                        $user_data[$key]['Popular_Count'] = $check_live_count;
                        $user_data[$key]['Popular_Tag'] = "No Popular User";
                    }
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $user_data[$key]['blockflag'] = '1';
                    } else {
                        $user_data[$key]['blockflag'] = '0';
                    }
                    if (!empty($value['follow_unfollow_flag'])) {
                        $user_data[$key]['follow_flge'] = '1';
                    } else {
                        $user_data[$key]['follow_flge'] = '0';
                    }
                    unset($user_data[$key]['follow_unfollow_flag']);
                    unset($user_data[$key]['live_streaming_flag']);
                }
                if(sizeof($user_data) > 0){
                    $data['user'] = $user_data;
                    Log::info(json_encode($msg));
                    Log::info(json_encode($data));
                    Log::info(json_encode($data1));
                    return response()->json(['status' => 'success','message' => $msg,'data' => $data, 'Popular User Data' => $data1]);
                } else {
                    Log::info(json_encode($msg1));
                    Log::info(json_encode($data1));
                    
                    return response()->json(['status' => 'success','message' => $msg1, 'Popular User Data' => $data1]);
                }
            }catch(\Exception $e) {
                return response()->json(['status'  => 'error','message' => $e->getMessage()]);
            }
        }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Search User
    -------------------------------------------------------------------------------------------- */
     public function searchuser(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $checkblockuser = Helper::CheckBlockUser($user);
            $sendcheckblockuser = Helper::SendCheckBlockUser($user);
            if($request->serachingValue){
                $user_data = User::where('id','!=',$user->id)->where('username', 'like', "%{$request->get('serachingValue')}%")->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('username','ASC')->get()->toArray();
                $data1 = array();

            if(sizeof($user_data) > 0){
                $data = $user_data;
                    return response()->json(['status' => 'success','message' => 'Record Successfully...!','data' => $data]);
            } else {
                return response()->json(['status' => 'success','message' => 'Not Record Found...!']);
            }
            } else {
                $user_data = User::where('id','!=',$user->id)->where('user_type','!=',$user->id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('username','ASC')->get()->toArray();
            }

            Log::info(json_encode($user_data));                    
            return response()->json(['status' => 'success','message' => 'All users', 'data' => $user_data]);
        }catch(\Exception $e) {
            return response()->json(['status'  => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Gift
    -------------------------------------------------------------------------------------------- */
    public function GetGift(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able from this application...'],200);
            }
            $gift = Gift::get();

            Log::info(json_encode($gift));                    
            return response()->json(['status' => 'success','message' => "Gift Successfully",'data' => $gift]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Banner
    -------------------------------------------------------------------------------------------- */
    public function getBanner(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $BannerDetail = Banner::where('status','active')->orderBy('id','DESC')->get();

            Log::info(json_encode($BannerDetail));
            return response()->json(['status'  => 'success','message' => 'Banner Detail Successfully', 'data' => $BannerDetail]);
        }catch (\Exception $e) {
            return response()->json(['status'  => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Video of users
    -------------------------------------------------------------------------------------------- */
    public function usersvideo(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able from this application...'],200);
            }
                $checkblockuser = Helper::CheckBlockUser($user);
                $sendcheckblockuser = Helper::SendCheckBlockUser($user);

                $video_listing = User::with(['follow_unfollow_flag' => function($query) use($user){
                    $query->where('followed_user_id', $user->id);
                }])->WithVideoData()->where('id',$request->user_id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('id','DESC')->get()->toArray();
                foreach ($video_listing as $key => $value1) {
                    $videos_list = Videos::where('user_id', $value1['id'])->first();
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    $video_listing[$key]['video'] = $videos_list->video;
                    $video_listing[$key]['thum'] = $videos_list->thum;
                    $video_listing[$key]['user_flag'] = $videos_list->user_flag;
                    $video_listing[$key]['admin_flag'] = $videos_list->admin_flag;
                    if(!empty($blockflag_user->blocked_user_id)){
                        $video_listing[$key]['blockflag'] = '1';
                    } else {
                        $video_listing[$key]['blockflag'] = '0';
                    }
                    if (!empty($value1['follow_unfollow_flag'])) {
                        $video_listing[$key]['follow_flge'] = '1';
                    } else {
                        $video_listing[$key]['follow_flge'] = '0';
                    }
                    unset($video_listing[$key]['follow_unfollow_flag']);
                }
                    $data1['Videos'] = $video_listing;

            return response()->json(['status' => 'success','message' => "Videos Successfully",'data' => $video_listing]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }
}
