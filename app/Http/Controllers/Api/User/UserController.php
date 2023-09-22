<?php

namespace App\Http\Controllers\Api\User;
use App\Jobs\sendNotification;
use App\Models\Popular;
use App\Models\VideoUpload;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetRequest;
use App\Helpers\GlobalH;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ixudra\Curl\Facades\Curl;
use Carbon\Carbon;
use App\Models\Setting;
use App\Models\Otp;
use App\Models\LiveComment;
use App\Models\Notifications;
use App\Models\CardDetails;
use App\Models\Diamond;
use App\Helpers\Helper;
use App\Models\GiftDiamond;
use App\Models\Gift;
use App\Models\LiveNotification;
use App\Models\LiveData;
use App\Models\Transaction;
use App\Models\BlockUserList;
use App\Models\ReportCategory;
use App\Models\ReportUser;
use App\Models\FollowUser;
use App\Models\Cms;
use App\Models\Conversions;
use App\Models\Videos;
use Event;
use PushNotification;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\Log;

class UserController extends Controller{
    // public function __construct(){
    //     $a = getallheaders();
    //     $b = getallheaders();
    //     $app_version =  $a['app_version'];
    //     $device_type =  $b['device_type'];
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
    //             // dd("in");
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

            // for pkmode
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

            // for normal
            
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

            // for pkmode
            
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

            // for normal
            

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
    @Description: Function for Login
    -------------------------------------------------------------------------------------------- */

    public function login(Request $request){
        if($request->social_media && $request->social_id){

            $validation_array = [
                'social_media'      => 'required',
                'social_id'         => 'required',
            ];
        }else{
            $validation_array = [
                'email'         => 'required',
                'password'      => 'required|min:6',
                'device_type'   => 'required',
                'device_token'  => 'required',
            ];
        }
        $validation = Validator::make($request->all(),$validation_array);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->messages()->first()],200);
        }
        try{
            //Social Login
            if($request->social_media && $request->social_id){
                $user = User::where(['social_id'=>request('social_id')])->first();
                if(empty($user)){

                $user = User::firstOrCreate([                    
                    "email"    =>request('email')

                ]);

                $joindiamond = Setting::where('code','joindiamond')->first();

                $user->username = request('username');
                $user->social_id = request('social_id');
                $user->social_media = request('social_media');
                $user->gender = request('gender');
                $user->first_name = request('first_name');
                $user['diamond'] = $joindiamond->value;
                if($user->is_verify == '0'){
                    $token = mt_rand(1000, 9999);
                    $user->otp = $token;
                }
                try {
                    if($request->avatar){
                        $filename = Str::random(10).'.jpg';
                        file_put_contents(storage_path().'/app/public/avatar/' . $filename, file_get_contents($request->avatar));
                        $user->avatar = $filename;
                    } else {
                        $user->avatar = "default.png";
                    }
                }catch (\Intervention\Image\Exception\NotReadableException $e) {
                }
                
                $user->save();
                $token = JWTAuth::fromUser($user);
                $userdata = User::where('id',$user->id)->first();
                $userdata->device_token = $request->device_token;
                $userdata->device_type = $request->device_type;
                // $userdata->first_name = $request->first_name;
                $userdata->status = 'active';
                $userdata->user_type = 'user';
                $userdata->otp_varifiy = '1';
                $userdata->save();
                $data['token']=$token;
                $data['user']=$userdata;
                
                Log::info(json_encode($data));
                return response()->json(['status' => 'success','message' => 'Login Successfully Done..!','data'=>$data], 200);
                } else {
                    $token = JWTAuth::fromUser($user);
                $userdata = User::where('id',$user->id)->first();
                $userdata->device_token = $request->device_token;
                $userdata->device_type = $request->device_type;
                // $userdata->first_name = $request->first_name;
                $userdata->status = 'active';
                $userdata->user_type = 'user';
                $userdata->otp_varifiy = '1';
                $userdata->save();
                $data['token']=$token;
                $data['user']=$userdata;
                
                Log::info(json_encode($data));
                return response()->json(['status' => 'success','message' => 'Login Successfully Done..!','data'=>$data], 200);
                }
            }else{
            
            //Normal Login
            
                if(filter_var($request->email, FILTER_VALIDATE_EMAIL) ){
                    $credentials = $request->only('email', 'password','user_type');
                }else{
                    $credentials =[ 'contact_number'=>$request->email,'password'=>$request->password ,'user_type'=>$request->user_type];
                }
                $data= [];
                try {
                    if(! $token = JWTAuth::attempt($credentials)) {
                        return response()->json(['status' => 'error','message' => 'Please try again with Correct Details', 'data' => (object)[]], 200);
                    }
                    if(filter_var($request->email, FILTER_VALIDATE_EMAIL) ){
                        $userTypeCheck = User::where('email',$request->get('email'))->where('status','active')->first();
                    }else{
                        $userTypeCheck = User::where('contact_number',$request->get('email'))->where('status','active')->first();
                    }
                    if(!empty($userTypeCheck)){
                        if($userTypeCheck->user_type == 'user'){
                            if($userTypeCheck->status != 'active'){
                                return response()->json(['status' => 'error','message' => 'You are not able to login in this Application','data' => (object)[]], 200);
                            }
                        }
                    }else{
                        if(filter_var($request->email, FILTER_VALIDATE_EMAIL) ){
                            $userTypeCheck = User::where('email',$request->get('email'))->where('status','inactive')->first();
                        }else{
                            $userTypeCheck = User::where('contact_number',$request->get('email'))->where('status','inactive')->first();
                        }
                        $data['object'] = (object)[];
                        if(!empty($userTypeCheck->reason_for_inactive)){
                            $data['reason_for_inactive'] = $userTypeCheck->reason_for_inactive;
                        } else {
                            $data['reason_for_inactive'] = "You have Violated the our Policies so your Account.";
                        }
                        return response()->json(['status' => 'error','message' => 'You are not able to login this application because of '.$data['reason_for_inactive'],'data' => (object)[]], 200);
                    }
                }catch (JWTException $e) {
                    return response()->json(['status' => 'error','message' => 'could_not_create_token', 'data' => (object)[]], 200);
                }
                // dd($userTypeCheck->otp_varifiy == '0');
                if($userTypeCheck->otp_varifiy == '0'){
                    if($userTypeCheck->user_type == 'user'){
                    $data['token'] = $token;
                    $data['user'] = $userTypeCheck;
                    $userTypeCheck->device_token = $request->device_token;
                    $userTypeCheck->device_type = $request->device_type;
                    $userTypeCheck->save();

                    $otpNumber = random_int(1000, 9999);
                    //$otpNumber = (1234);
                    $checkContactNumInUser = User::where('contact_number',$userTypeCheck->contact_number)->first();
                    if($checkContactNumInUser !== null){
                        $checkIfUserOtpExist = Otp::where('email',$checkContactNumInUser->email)->where('contact_number',(string)$checkContactNumInUser->contact_number)->first();
                        if($checkIfUserOtpExist !== null){
                            Otp::where('id',$checkIfUserOtpExist->id)
                                ->where('contact_number',(string)$checkContactNumInUser->contact_number)
                                ->where('email',$checkContactNumInUser->email)
                                ->update([
                                    'otp_number'    => $otpNumber,
                                    'otp_expire'    => $checkIfUserOtpExist->updated_at->addSeconds(180)
                                ]);
                        }else{
                            $UserOtpCreated = Otp::create([
                                'email'         => $checkContactNumInUser->email,
                                'contact_number' => (string)$checkContactNumInUser->contact_number,
                                'otp_number'    => $otpNumber,
                            ]);
                            Otp::where('id',$UserOtpCreated->id)->update([
                                'otp_expire'    => $UserOtpCreated->created_at->addSeconds(180)
                            ]);
                        }
                        $text = 'Your OTP is: '.$otpNumber;
                        $emailcontent = array (
                            'text' => $text,
                            'title' => 'Thanks for Joining wonga Live, Please use OTP for Completion of SignUp Process. You will need OTP to complete Sign Up Process.',
                            'userName' => $checkContactNumInUser->first_name
                        );
                        $details['email'] = $checkContactNumInUser->email;
                        $details['username'] = $checkContactNumInUser->first_name;
                        $details['subject'] = 'OTP Confirmation';
                        dispatch(new sendNotification($details,$emailcontent));
                        $data['otpNumber'] = $otpNumber;
                    }
                    // sent otp code
                    Log::info(json_encode($data));
                    return response()->json(['status' => 'success','message' => 'Login successfully','data'=>$data], 200);
                }
            }else{
                // Verified User
                $data['token'] = $token;
                $data['user'] = $userTypeCheck;
                $userTypeCheck->device_token = $request->device_token;
                $userTypeCheck->device_type = $request->device_type;
                $userTypeCheck->save();

                if($data['user']->avatar != "default.png" and !empty($data['user']->avatar)){
                    $data['user']['avatar'] = $data['user']->avatar;
                } else {
                    if($data['user']->gender == "male"){
                        $data['user']['avatar'] = "male.png";
                    } elseif($data['user']->gender == "female") {
                        $data['user']['avatar'] = "female.png";
                    } else {
                        $data['user']['avatar'] = "default.png";
                    }
                }
                Log::info(json_encode($data));
                return response()->json(['status' => 'success','message' => 'Login successfully','data'=>$data], 200);
            }
        }
    }catch (Exception $e) {
        return response()->json(['status' => 'error','message' => 'Something went Wrong..!', 'data' => (object)[]],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Registration
    -------------------------------------------------------------------------------------------- */

        public function register(Request $request){
            $validation_array =[
                'email'                => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'username'             => 'required',
                'contact_number'       => 'required|unique:users,contact_number,NULL,id,deleted_at,NULL',
                'name'                 => 'required',
                'password'             => 'min:8|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation'=> 'required',
                'device_token'         => 'required',
                'gender'               => 'required',
            ];
            $validation = Validator::make($request->all(),$validation_array);
            if($validation->fails()){
                return response()->json(['status' => 'error','message'   => $validation->errors()->first(),'data'=> (object)[]]);
            }
            try {
                if($request->hasFile('avatar')){
                    $file = $request->file('avatar');
                    $extension = $file->getClientOriginalExtension();
                    $filename = Str::random(10).'.'.$extension;
                    Storage::disk('public')->putFileAs('avatar', $file,$filename);
                }else{
                    $filename = 'default.png';
                }
                $joindiamond = Setting::where('code','joindiamond')->first();

                $data['avatar']             =$filename;
                $data['email']              =request('email');
                $data['username']           =request('username');
                $data['contact_number']     =request('contact_number');
                $data['first_name']         =request('name');
                $data['password']           =bcrypt(request('password'));
                $data['user_type']          ='user';
                $data['status']             ='active';
                $data['sign_up_as']         ='app';
                $data['device_type']        =request('device_type');
                $data['device_token']       =request('device_token');
                $data['gender']             =request('gender');
                $data['diamond']            =$joindiamond->value;
                $userdata = User::Create($data);
                $user = User::where('id',$userdata->id)->first();
                $data1['token'] = JWTAuth::fromUser($userdata);
                $data1['user'] = $user;
                $otpNumber = '';
                if(!empty($userdata)){
                    $otpNumber = random_int(1000, 9999);
                    //$otpNumber = (1234);
                    $UserOtpCreated = Otp::create([
                        'email'         => $user->email,
                        'contact_number' => (string)$user->contact_number,
                        'otp_number'    => $otpNumber,
                    ]);
                    $text = 'Your OTP is: '.$otpNumber;
                    $emailcontent = array (
                        'text' => $text,
                        'title' => 'Thanks for Joining wonga Live, Please use Below OTP for Contact Verification.',
                        'userName' => $user->first_name
                    );
                    $details['email'] = $user->email;
                    $details['username'] = $user->first_name;
                    $details['subject'] = 'Welcome to wonga Live, OTP Confirmation';
                    dispatch(new sendNotification($details,$emailcontent));
                }
                $data1['otpNumber'] = $otpNumber;
                $data['message'] =  'User Registration';
                $data['type'] = 'registered';
                $data['user_id'] = $userdata->id;

                Log::info(json_encode($data1));
            return response()->json(['status' => 'success','message' => 'You are successfully Register..!','data' => $data1]);
        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Send Otp
    -------------------------------------------------------------------------------------------- */

    public function sendOtp(Request $request){
        $validator =  Validator::make($request->all(),[
            'contact_number' => 'required|min:7'
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','message'=> $validator->messages()->first()]);
        }
        try{
            $otpNumber = random_int(1000, 9999);
            //$otpNumber = (1234);
            $checkContactNumInUser = User::where('contact_number',$request->get('contact_number'))->first();
            if($checkContactNumInUser !== null){
                $checkIfUserOtpExist = Otp::where('email',$checkContactNumInUser->email)->where('contact_number',(string)$checkContactNumInUser->contact_number)->first();
                if($checkIfUserOtpExist !== null){
                    Otp::where('id',$checkIfUserOtpExist->id)
                        ->where('contact_number',(string)$checkContactNumInUser->contact_number)
                        ->where('email',$checkContactNumInUser->email)
                        ->update([
                            'otp_number'    => $otpNumber,
                            'otp_expire'    => $checkIfUserOtpExist->updated_at->addSeconds(180)
                        ]);
                }else{
                    $UserOtpCreated = Otp::create([
                        'email'         => $checkContactNumInUser->email,
                        'contact_number' => (string)$checkContactNumInUser->contact_number,
                        'otp_number'    => $otpNumber,
                    ]);
                    Otp::where('id',$UserOtpCreated->id)->update([
                        'otp_expire'    => $UserOtpCreated->created_at->addSeconds(180)
                    ]);
                }
                $text = 'Your OTP is: '.$otpNumber;
                $emailcontent = array (
                    'text' => $text,
                    'title' => 'Thanks for Joining wonga Live, Please use Below OTP for Completion of SignUp Process. You will need OTP to complete Sign Up Process.',
                    'userName' => $checkContactNumInUser->first_name
                );
                $details['email'] = $checkContactNumInUser->email;
                $details['username'] = $checkContactNumInUser->first_name;
                $details['subject'] = 'OTP Confirmation';
                dispatch(new sendNotification($details,$emailcontent));
            }else{
                return response()->json(['status' => 'error','message'=> 'Mobile Number does not Exist.']);
            }
            Log::info(json_encode($otpNumber));
            return response()->json(['status'=> 'success','message' => 'Otp Sent Successfully..!','data'=> $otpNumber ]);
        }catch (\Exception $exception){
            return response()->json(['message'=> $exception->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Verify Otp
    -------------------------------------------------------------------------------------------- */

    public function verifyOtp(Request $request){
        $validator =  Validator::make($request->all(),[
            'contact_number' => 'required|min:7',
            'otp_number'    => 'required|max:4|min:4'
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','message'   => $validator->messages()->first()]);
        }
        try{
    
            $getOtpData = Otp::where('otp_number',$request->get('otp_number'))->where('contact_number',$request->get('contact_number'))->first();

            
            if($getOtpData !== null){
                if(Carbon::now() >= Carbon::parse()){
                    return response()->json(['status' => 'error','message' => 'Otp Expired']);
                }
                $getOtpuser1 = Otp::with('user')->where('contact_number',$request->get('contact_number'))->first();
                User::where('id', $getOtpuser1->user->id)->update(['otp_varifiy' => "1"]);
                // $getOtpuser = User::where('id', $getOtpuser1->user->id)->first();
                $getOtpuser = Otp::with('user')->where('contact_number',$request->get('contact_number'))->first();
                return response()->json(['status'    => 'success','message'   => 'OTP is Verified.','data' => $getOtpuser]);
            }
            return response()->json(['status'    => 'error','message'   => 'Invalid Otp',]);
        }catch (\Exception $exception){
            return response()->json(['message'   => $exception->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Own User Profile
    -------------------------------------------------------------------------------------------- */

    public function getProfile(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $user_data = User::where(['id'=>$user->id])->first();
            $data['user']  = $user_data;

            Log::info(json_encode($data));
            return response()->json(['status' => 'success','message' => 'User Profile Successfull','data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Update Own Profile
    -------------------------------------------------------------------------------------------- */

    public function updateProfile(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $user_data = User::where('id',$user->id)->first();
            if($user_data){
                if($request->hasFile('avatar')){
                    $file = $request->file('avatar');
                    $extension = $file->getClientOriginalExtension();
                    $filename = Str::random(10).'.'.$extension;
                    Storage::disk('public')->putFileAs('avatar', $file,$filename);
                }else if($user_data->avatar){
                    $filename = $user_data->avatar;
                }else{
                    $filename = '';
                }
                $checkcontactexist = User::where('contact_number', request('contact_number'))->first();
                if(!empty($checkcontactexist) && ($checkcontactexist->id !== $user->id)){
                    return response()->json(['status' => 'error','message' => 'Contact No already has been taken.']);
                }
                if(request('contact_number')){
                    $contact_number = request('contact_number');
                }else{
                    $contact_number = $user_data->contact_number;
                }
                $user_data->email           = request('email');
                $user_data->username      = request('username');
                $user_data->contact_number  = $contact_number;
                $user_data->first_name       = request('name');
                $user_data->avatar          = $filename;
                $user_data->save();
                
                $data ['user'] = $user_data;

                Log::info(json_encode($data));
                return response()->json(['status' => 'success','message' => 'Profile Update Successfully..!','data' => $data]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()], 200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Change Password
    -------------------------------------------------------------------------------------------- */

    public function changePassword(Request $request){
        $validation_array =[
            'old_password'        => 'required|string|min:6',
            'new_password'        => 'required|string|min:6',
            'confirm_password'    => 'required|string|min:6',
        ];
        $validation = Validator::make($request->all(),$validation_array);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->messages()->first()],200);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status' => 'error','message' => "Invalid Token..."],200);
            }
            if($user !== null){
                $password     = $user->password;
                $old_password = request('old_password');
                $new_password = request('new_password');
                $c_password   = request('confirm_password');
                if($new_password != $c_password){
                    return response()->json(['status' => 'error','message' => 'Your Password does not match with Above Password']);
                }
                if(isset($password)) {
                    if($old_password == $new_password){
                        return response(['status' => 'error','message'=>'New Password cannot be same as your current password. Please choose a different password.']);
                    }else{
                        if(\Hash::check($old_password, $password)){
                            $user->password = \Hash::Make($new_password);
                            $user->save();
                            return response()->json(['status' => 'success','message' => 'Your password change Successfully..!']);
                        }else{
                            return response()->json(['status' => 'error','message' => 'Your current password does not matches with the password you provided. Please try again.']);
                        }
                    }
                }else{
                    return response()->json(['status' => 'error','message' => 'User not available.']);
                }
            }else{
                return response()->json(['status'    => 'error','message'   => "You are not able login from this "]);
            }
        }catch(\Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Logout
    -------------------------------------------------------------------------------------------- */

    public function logout(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $user = User::find($user->id);
            // User::where('id',$user->id)->update(['available_flag' => 'offline']);
            User::where('id',$user->id)->update(['device_token' => null]);
            $user->save();
            JWTAuth::invalidate($request->token);
            return response()->json(['status'  => 'success','message' => 'User logged out Successfull..!']);
        }catch (\Exception $e) {
            return response()->json(['status'  => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Notifications
    -------------------------------------------------------------------------------------------- */

    public function getNotifications(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $notifications = Notifications::with('receive_data')->where('follow_user',$user->id)->orderBy('id','desc')->limit('20')->get();
            foreach ($notifications as $key => $value) {
                    if (!empty($value['receive_data'])) {
                        $notifications [$key]['username'] = $value->receive_data->username;
                        $notifications [$key]['avatar'] = $value->receive_data->avatar;
                    }
                    unset($notifications[$key]['receive_data']);
                }
            // $notifications_live = Notifications::where('follow_user',$user->id)->where('flag_status','live')->limit('20')->orderBy('id','desc')->get()->toArray();
            // $notifications_flag = Notifications::where('follow_user',$user->id)->whereIn('follow_status',['unfollow','follow'])->limit('20')->orderBy('id','desc')->get()->toArray();
            // $users = array_merge($notifications_live,$notifications_flag);
            // array_multisort( array_column($users, "id"), SORT_DESC, $users);

                Log::info(json_encode($notifications));
            return response()->json(['status'  => 'success','message' => 'User Notifications..!', 'data' => $notifications]);
        }catch (\Exception $e) {
            return response()->json(['status'  => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Notifications
    -------------------------------------------------------------------------------------------- */
        public function readunreadNotifications(Request $request){
            $validation_array =[
            'notification_id' => 'required',
            'status' => 'required',

            ];
            $validation = Validator::make($request->all(),$validation_array);
            if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->messages()->first()],200);
            }
            try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
            return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $notifications = Notifications::where('id',$request->notification_id)->update(['status' => $request->status]);

            return response()->json(['status' => 'success','message' => 'User Notifications..!', 'data' => "update notifications"]);
            }catch (\Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
            }
        }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Other Profile Details
    -------------------------------------------------------------------------------------------- */

    public function getUserDetail(Request $request){
            $validation_array =[
                'user_id'        => 'required',
            ];
            $validation = Validator::make($request->all(),$validation_array);
            if($validation->fails()){
                return response()->json(['status' => 'error','message' => $validation->messages()->first()],200);
            }
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                    return response()->json(['status'=>'error','message' => 'You are not able from this application...'],200);
                }
                $checkblockuser = Helper::CheckBlockUser($user);
                $sendcheckblockuser = Helper::SendCheckBlockUser($user);
                // User::where('id',$user->id)->update(['available_flag' => 'online']);

                $user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                        $query->where('followed_user_id', $user->id);
                    }])->withPackageAmount()->where('id',$request->user_id)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->get()->toArray();
                $msg = 'User Found Successfully...!';
                $msg1 = 'No user Found...!';
                foreach ($user_data as $value) {
                    $checkUserLive = LiveNotification::where('user_id', $value['id'])->whereIn('status', ['pending','online'])->orderBy('id','desc')->first();
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->where('blocked_user_id',$request->user_id)->first();
                    $followers_count = FollowUser::where('user_id',$user->id)->count('followed_user_id');
                    $following_count = FollowUser::where('followed_user_id',$user->id)->count('user_id');

                    // dd($folowing_count);
                    

                    if(!empty($checkUserLive->u_id)){
                        $value['live_streaming_flge'] = '1';
                        $value['channel_id'] = $checkUserLive->u_id;
                        if(!empty($blockflag_user)){
                            $value['blockflag'] = '1';
                        } else {
                            $value['blockflag'] = '0';
                        }
                    } else {
                        $value['live_streaming_flge'] = '0';
                        $value['channel_id'] = '0';
                        if(!empty($blockflag_user)){
                            $value['blockflag'] = '1';
                        } else {
                            $value['blockflag'] = '0';
                        }
                    }
                    if (!empty($value['follow_unfollow_flag'])) {
                        $value['follow_flge'] = '1';
                    } else {
                        $value['follow_flge'] = '0';
                    }

                    $value['followers_count'] = (string)$followers_count;
                    $value['following_count'] = (string)$following_count;

                    unset($value['follow_unfollow_flag']);
                    unset($value['live_streaming_flag']);

                    $data['user'] = $value;
                }
                    Log::info(json_encode($value));
                    return response()->json(['status'  => 'success','message' => 'User Detail Successfully', 'data' => $value]);
            } catch (Exception $e) {
                return response()->json(['status'  => 'error','message' => $e->getMessage()]);
            }
        }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Diamonds
    -------------------------------------------------------------------------------------------- */

    public function getDiamonds(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $DiamondDetail = Diamond::where('status','active')->get();

            Log::info(json_encode($DiamondDetail));
            return response()->json(['status'  => 'success','message' => 'Diamond Detail Successfully', 'data' => $DiamondDetail]);
        }catch (\Exception $e) {
            return response()->json(['status'  => 'error','message' => $e->getMessage()]);
        }
    }

    

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Add Use Card Details
    -------------------------------------------------------------------------------------------- */

    public function add_CardDetails(Request $request){
        $validator = [
            'card_number'          =>'required|integer',
            'card_holder_name'     =>'required',
            'card_expiry_month'    =>'required',
            'card_expiry_year'     =>'required',
            'card_type'            =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status'    => 'error','message'   => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $check_add_card = CardDetails::where('user_id',$user->id)->where('card_number',$request->card_number)->first();
            if(!empty($check_add_card)){
                return response()->json(['status'=>'error','message'=>'Already Card Added...!']);
            }else{
            $card_details = CardDetails::create([
                'user_id'           => $user->id,
                'card_number'       => request('card_number'),
                'card_holder_name'  => request('card_holder_name'),
                'card_expiry_month' => request('card_expiry_month'),
                'card_expiry_year'  => request('card_expiry_year'),
                'card_type'         => request('card_type'),
                'card_name'         => request('card_type'),
                'cvv'               => request('cvv')
            ]);

            Log::info(json_encode($card_details));
            return response()->json(['status'=>'success','message'=>'Card Details Added Successfully..!','data'=>$card_details]);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Edit User Card Details
    -------------------------------------------------------------------------------------------- */

 public function edit_CardDetails(Request $request){
        $validator = [
            'card_id'  =>'required',
            'card_number'          =>'required|integer',
            'card_holder_name'     =>'required',
            'card_expiry_month'    =>'required',
            'card_expiry_year'     =>'required',
            'card_type'            =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message'   => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            // dd($user->id);
            $check_edit_card = CardDetails::where('user_id',$user->id)->where('card_number',$request->card_number)->first();
            // dd($check_edit_card);
            if(!empty($check_edit_card)){
                $card_details = CardDetails::where('user_id',$user->id)->where('id',request('card_id'))->first();
                if($card_details->card_number == $request->card_number)
                {
                $card_details->user_id           = $user->id;
                $card_details->card_number       = request('card_number');
                $card_details->card_holder_name  = request('card_holder_name');
                $card_details->card_expiry_month = request('card_expiry_month');
                $card_details->card_expiry_year  = request('card_expiry_year');
                $card_details->card_type         = request('card_type');
                $card_details->card_name         = request('card_type');
                $card_details->cvv               = request('cvv');
                $card_details->save();

                Log::info(json_encode($card_details));
                return response()->json(['status'=>'success','message'=>'Details Updated..!','data'=>$card_details]);
                } else {
                   return response()->json(['status'=>'error','message'=>'Already Card Details Found..!']);
                }
                // return response()->json(['status'=>'error','message'=>'Already Card Details Found']);
            }else{
                $card_details = CardDetails::where('user_id',$user->id)->where('id',request('card_id'))->first();
                $card_details->user_id           = $user->id;
                $card_details->card_number       = request('card_number');
                $card_details->card_holder_name  = request('card_holder_name');
                $card_details->card_expiry_month = request('card_expiry_month');
                $card_details->card_expiry_year  = request('card_expiry_year');
                $card_details->card_type         = request('card_type');
                $card_details->card_name         = request('card_type');
                $card_details->cvv               = request('cvv');
                $card_details->save();

                Log::info(json_encode($card_details));
                return response()->json(['status'=>'success','message'=>'User Card Details Updated Successfully..!','data'=>$card_details]);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }
    /* -----------------------------------------------------------------------------------------
    @Description: Function for Delete User Card Details
    -------------------------------------------------------------------------------------------- */

    public function delete_CardDetails(Request $request){
        $validator = [
            'card_id'   =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status'    => 'error','message'   => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $card_details = CardDetails::where('user_id',$user->id)->where('id',request('card_id'))->first();
            if($card_details){
                $card_details->delete();
                return response()->json(['status'=>'success','message'=>'Card Details Deleted Successfully..!']);
            }else{
                return response()->json(['status'=>'error','message'=>'No Card Details Found']);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for View User Card Details
    -------------------------------------------------------------------------------------------- */

    public function view_CardDetails(Request $request){
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $card_details = CardDetails::where('user_id',$user->id)->get();
            if(count($card_details) != 0){

                Log::info(json_encode($card_details));
                return response()->json(['status'=>'success','message'=> 'Card Details Getting Successfully..!','data'=> $card_details]);
            }else{
                return response()->json(['status'=> 'error','message'=>'No Card Details Found']);
            }
        }catch(Exception $e){
            return response()->json(['status'=> 'error','message'  => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Default User Card
    -------------------------------------------------------------------------------------------- */

    public function default_CardDetails(Request $request){
        $validator = [
            'card_id'   =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status'    => 'error','message'   => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $card_default = CardDetails::where('user_id',$user->id)->where('id',request('card_id'))->first();
            $card_notdefault = CardDetails::where('user_id',$user->id)->where('id','!=',request('card_id'))->pluck('id');
            if($card_default){
                $card_default->default_status = '1';
                $card_default->save();
                CardDetails::whereIn('id', $card_notdefault)->update(['default_status' => '0']);

                Log::info(json_encode($card_default));
                return response()->json(['status'=>'success','message'=> 'Card Details Addedd in Default','data'=> $card_default]);
            }else{
                return response()->json(['status'=> 'error','message'=>'No Card Details Found']);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for User Diamond Payment
    -------------------------------------------------------------------------------------------- */

    public function paymenthistory(Request $request){
        $validator = Validator::make($request->all(),[
            'diamonds_id'       => 'required',
            'transaction_id'    => 'required',
            'card_id'           => 'required',
            'amount'            => 'required',
            'status'            => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status'    => 'error','message'   => $validator->messages()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able from this application...'],200);
            }
            $diamonds = Diamond::where('id',$request->diamonds_id)->first();
            
                     PaymentHistory::Create([
                    "user_id"         =>$user->id,
                    "diamonds_id"     =>request('diamonds_id'),
                    "transaction_id"  =>request('transaction_id'),
                    "card_id"         =>request('card_id'),
                    "amount"          =>request('amount'),
                    "packs"           =>$diamonds->packs,
                    "payment_status"  =>request('status'),
                    "user_purch_date" =>date("Y/m/d H:m:s"),
                ]);
            
            
            $user = User::where('id', $user->id)->first();

            
            if(!empty($user) && $user->diamond){
            $userDiamond = (int)$user->diamond + (int)$diamonds->packs;
            User::where('id', $user->id)->update([ 'diamond' => $userDiamond ]);
            }else{
            User::where('id', $user->id)->update([ 'diamond' => $diamonds->packs ]);
            }

            return response()->json(['status' => 'success','message' => 'You Payment is Successfully Done..!']);
        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for User Diamond Payment History
    -------------------------------------------------------------------------------------------- */

    public function getpaymenthistory(){
        
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able from this application...'],200);
            }

            $payment = PaymentHistory::join('card_details', 'card_details.id', '=', 'paymenthistory.card_id')
            ->join('diamonds', 'diamonds.id', '=', 'paymenthistory.diamonds_id')
            ->where('paymenthistory.user_id',$user->id)->orderBy('paymenthistory.id','desc')->get();
            

            if(!empty($payment)){

                Log::info(json_encode($payment));
                return response()->json(['status' => 'success','message' => 'Your Payment History', 'data'=>$payment]);
            }else{
                return response()->json(['status'=> 'error','message'=>'Payment History Not Found']);
            }
            
        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong....."],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for User Diamond Total Count
    -------------------------------------------------------------------------------------------- */
    public function countDiamond()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'You are not able from this application...'], 200);
            }
            $userpayment = User::where('id',$user->id)->first();
            $data['total_diamond'] = $userpayment->diamond;
            
            Log::info(json_encode($data));
            return response()->json(['status' => 'success','message' => 'Total Diamond Amount', 'total diamond'=>$data]);

        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200, );
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Report Category List
    -------------------------------------------------------------------------------------------- */

    public function reportCategory(Request $request){
        try{
            $allreportcategory = json_decode(strip_tags(ReportCategory::where('status','active')->get()),true);
            if($allreportcategory){

                Log::info(json_encode($allreportcategory));
               return response()->json(['status' => 'success','message' =>'All Report Category','data' => $allreportcategory]);
            }else{
                return response()->json(['status'=>'error','message'=>'No Report Category Found']);
            }
            
        }catch(Exception $e){
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Reported User List
    -------------------------------------------------------------------------------------------- */
    public function reportUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'category_id'       => 'required',
        // 'description'       => 'required',
        'receive_id'        => 'required',
        ]);
        if ($validator->fails()) {
              return response()->json(['status'   => 'error','message'  => $validator->messages()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $data = ReportUser::create([
                'category_id' => $request->category_id,
                'receive_id' => $request->receive_id,
                'user_id' => $user->id
                // 'description' => $request->description
                ]);

                Log::info(json_encode($data));
                return response()->json(['code' => '200','status' => 'success','message' =>'User Report Successfully Done','msg' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Block User Create
    -------------------------------------------------------------------------------------------- */

    public function blockUserCreate(Request $request){
        $validation_array =[
            'block_id'        => 'required',
            'status'          => 'required',
        ];
        $validation = Validator::make($request->all(),$validation_array);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->messages()->first()],200);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            if($request->status == "block"){
                $checkuserblock = BlockUserList::where('user_id',$user->id)->where('blocked_user_id',request('block_id'))->first();
                if(empty($checkuserblock)) {
                    BlockUserList::create([
                        'user_id' => $user->id,
                        'blocked_user_id'   => $request->block_id,
                        ]);

                    $check_followedblock = FollowUser::where('user_id',$request->block_id)->where('followed_user_id',$user->id)->first();
                    if (!empty($check_followedblock)) {
                        $check_followedblock->delete();
                    }

                    $check_followingblock = FollowUser::where('followed_user_id',$request->block_id)->where('user_id',$user->id)->first();
                    if (!empty($check_followingblock)) {
                        $check_followingblock->delete();
                    }

                    $check_notification1 = Notifications::where('user_id',$request->block_id)->where('follow_user',$user->id);
                    if (!empty($check_notification1)) {
                        $check_notification1->delete();
                    }
    
                    $check_notification2 = Notifications::where('follow_user',$request->block_id)->where('user_id',$user->id);
                    if (!empty($check_notification2)) {
                        $check_notification2->delete();
                    }

                    $msg = "You Have Blocked User Successfully..!";
                }
            } else {
                $blockuserlist = BlockUserList::where('user_id',$user->id)->where('blocked_user_id',request('block_id'))->first();
                $blockuserlist->delete();
                $msg = "You Have UnBlocked User Successfully..!";
            }

            Log::info(json_encode($msg));
            return response()->json(['status' => 'success','message' => $msg]);            
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Block User List
    -------------------------------------------------------------------------------------------- */

    public function blockUserList(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $get_user_list = BlockUserList::with(['blockedUser'])->where('user_id' ,$user->id)->get();
            foreach ($get_user_list as $key => $value) {
                    if (!empty($value['blockedUser'])) {
                        $get_user_list [$key]['username'] = $value->blockedUser->username;
                        $get_user_list [$key]['avatar'] = $value->blockedUser->avatar;
                    }
                    unset($get_user_list[$key]['blockedUser']);
                }
                Log::info(json_encode($get_user_list));
            return response()->json(['status' => 'success','message' => "Your Blocked User List", 'data' => $get_user_list]);
            
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Create User Following
    -------------------------------------------------------------------------------------------- */
    public function createUserFollower(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'status'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'   => 'error','message'  => $validator->messages()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $check_follower = FollowUser::where('followed_user_id', $user->id)->where('user_id', $request->user_id)->first();
            if($request->status == "follow"){
                if (empty($check_follower)) {
                    $livebroadcheck = LiveNotification::where('user_id', $request->user_id)->where('status','!=','offline')->first();
                    if(!empty($livebroadcheck)){
                        $usercheck = User::where('id', $livebroadcheck->user_id)->first();
                        $userdetail = User::where('id', $user->id)->first();
                        $notification['message'] = @$usercheck->username.'Live Broadcating Started';
                        $notification['u_id']           = @$livebroadcheck->u_id;
                        $notification['type'] = 'Live Broadcating Started';
                        $notification['body'] = @$usercheck->username.' has been broadcasting Started.';
                        $notification['notification_type'] = 'live_broadcasting';
                        $notification['sender_id'] = @$usercheck->id;
                        $notification['title'] = 'Live Broadcating';
                        $notification['sound'] = 'default';
                         $user_data['notification'] = Event::dispatch('send-notification-assigned-user',array($userdetail,$notification));

              LiveNotification::create([
                        'user_id' => $usercheck->id,
                        'follow_user' => $userdetail->id,
                        'u_id' => $livebroadcheck->u_id,
                        'status' => 'pending',
                        ]);

                         Notifications::Create([
                         'user_id' => $usercheck->id,
                         'follow_user' => $userdetail->id,
                         'title'   => 'Live Streaming',
                         'description' => ' Started Live Broadacasting..!',
                         'flag_status' => 'live',
                         'channel_id' => @$livebroadcheck->u_id
                     ]);
            }

             
                    $message = "User Follower Successfully";
                    $follower = FollowUser::create([
                        'followed_user_id'    => $user->id,
                        'user_id'             => $request->user_id,
                        'status'             =>  "1",
                        ]);
                    $user_name = User::where('id',$user->id)->first();
                    $follow_user = User::where('id',$request->user_id)->first();
                    $notification['message'] =  @$user_name->username.' New follower Arrived';
                    $notification['type'] = 'New follower Arrived';
                    $notification['notification_type'] = 'New Following';
                    $notification['body'] = @$user_name->username. ' has followed You.';
                    $notification['user_id'] = @$follow_user->id;
                    $notification['title']        =  'wonga Live Biog';
                    $notification['sound']        = 'default';
                    $user_data['notification'] = Event::dispatch('send-notification-assigned-user',array($follow_user,$notification));

                    Notifications::Create([
                         'user_id' => $user->id,
                         'follow_user' => $request->user_id,
                         'title'   => 'User Gets Followed',
                         'description' => @$user->username.' Started Following You',
                         'follow_status' => 'follow'
                     ]);
                }
                
            } else {
                $follower = FollowUser::where('id',$check_follower->id)->first();
                $follower_user = User::find($follower->user_id);
                $user_name = User::where('id',$user->id)->first();
                $notification['message'] =  @$user_name->username. ' New unfollowed Arrived';
                $notification['type'] = 'New Unfollowing Arrived';
                $notification['notification_type'] = 'New Unfollowing';                
                $notification['body'] = @$user_name->username. ' has unfollowed You.';
                $notification['user_id'] = @$follower_user->id;
                $notification['title']        =  'wonga Live Biog';
                $notification['sound']        = 'default';
                $user_data['notification'] = Event::dispatch('send-notification-assigned-user',array($follower_user,$notification));

                Notifications::Create([
                         'user_id' => $user->id,
                         'follow_user' => $follower->user_id,
                         'title'   => 'User Gets Unfollowed',
                         'description' => @$user->username.' Unfollowed You',
                         'follow_status' => 'unfollow'
                     ]);    
                $message = "User Unfollowing Successfully";
                $follower->delete();
            }

            Log::info(json_encode($message));
            return response()->json(['status' => 'success','message' =>$message]);
            
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for User Unfollowing
    -------------------------------------------------------------------------------------------- */

    public function unfollowUser(Request $request)
    {
        $validator = [
            'user_id'   =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status'    => 'error','message'   => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $unfollowUser = FollowUser::where('followed_user_id',$user->id)->where('user_id', $request->user_id)->first();
            if($unfollowUser){
                $unfollowUser->delete();
                return response()->json(['status'=>'success','message'=>'Unfollowing User Successfully Done..!']);
            }else{
                return response()->json(['status'=>'error','message'=>'User Not Found']);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for User followers List
    -------------------------------------------------------------------------------------------- */

    public function getFollowers(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $all_followers = FollowUser::where('user_id', $user->id)
                ->whereHas('UserFollowerDataAll', function ($query) {
                })->get();

                // dd($all_followers);
            $value[] = '';
            foreach ($all_followers as $value) {
                $count_view_live = LiveNotification::where('user_id',$value->followed_user_id)->where('viewer','1')->groupby('user_id')->count('viewer');
                if (!empty($value->UserFollowerDataAll)) {
                    $value->UserFollowerDataAll['viewer_count'] = (string)$count_view_live;
                    $value->UserFollowerDataAll['follow'] = 'Following';
                    $value->UserFollowerDataAll['following_status_button'] = 'UnFollow';
                }
            }
            // die();
            Log::info(json_encode($all_followers));
            return response()->json(['status'=>'success','message'=>'User Followers List Successfully Done..!','data' => $all_followers]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for User following List
    -------------------------------------------------------------------------------------------- */

    public function getFollowing(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $all_following = FollowUser::where('followed_user_id', $user->id)
                ->whereHas('UserFollowingAll', function ($query) {
                })->get();
            $value[] = '';
            foreach ($all_following as $value) {
                $count_view_live = LiveNotification::where('user_id',$value->user_id)->where('viewer','1')->groupby('user_id')->count('viewer');
                if (!empty($value->UserFollowingAll)) {
                    $value->UserFollowingAll['viewer_count'] = (string)$count_view_live;
                    $value->UserFollowingAll['follow'] = 'Following';
                    $value->UserFollowingAll['following_status_button'] = 'UnFollow';
                }
            }
                Log::info(json_encode($all_following));
              return response()->json(['status'=>'success','message'=>'Users Following List Successfully Done..!','data' => $all_following]);
        } catch (Exception $e) {
             return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Live Search Follower
    -------------------------------------------------------------------------------------------- */

    public function LiveSearchFollower(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error','message' => $validator->messages()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            
            $jk = explode(',' ,$request->user_id);
            $userdetail = User::whereIn('id', $jk)->get();
            foreach ($userdetail as $value) {
                $usercheck = User::where('id', $user->id)->first();
                $livebroadcheck = LiveNotification::where('user_id', $user->id)->latest()->first();
                $notification['message'] = @$usercheck->username.' Live Broadcating Started';
                $notification['u_id'] = @$livebroadcheck->u_id;
                $notification['type'] = 'Live Broadcating Started';
                $notification['body'] = @$usercheck->username.' has invited you to watch live broadcast.';
                $notification['notification_type'] = 'live_broadcasting';
                $notification['sender_id'] = @$usercheck->id;
                $notification['title'] = 'Live Broadcating';
                $notification['sound'] = 'default';
                $user_data['notification'] = Event::dispatch('send-notification-assigned-user',array($value,$notification));
                $check_follower = FollowUser::where('followed_user_id', $user->id)->where('user_id', $value->id)->first();
                if(empty($check_follower)){
                    LiveNotification::create([
                        'user_id' => $usercheck->id,
                        'follow_user' => $value->id,
                        'u_id' => $livebroadcheck->u_id,
                        'status' => 'pending',
                    ]);

                    Notifications::Create([
                        'user_id' => $usercheck->id,
                        'follow_user' => $value->id,
                        'title' => 'Live Streaming',
                        'description' => @$usercheck->username.' Started Live Broadacasting..!',
                        'flag_status' => 'live',
                        'channel_id' => @$livebroadcheck->u_id
                    ]);
                }
            }
            return response()->json(['status' => 'success','message' =>"Live Streaming"]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Create Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */
        public function CreateLiveStreaming(Request $request)
        {
            $validator = [
                'u_id' =>'required',
                'filters' => 'required',
            ];
            $validation = Validator::make($request->all(),$validator);
            if($validation->fails()){
                return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
            }
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                    return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
                }
                $checkblockuser = Helper::CheckBlockUser($user);
                $sendcheckblockuser = Helper::SendCheckBlockUser($user);


                $all_following = FollowUser::where('user_id', $user->id)->whereNotIn('followed_user_id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->get();

                if($all_following->count() > 0){

                foreach ($all_following as $following) {
                $user_follow = User::find($following->followed_user_id);
                if(!empty($user_follow)){
                $notification['message'] = @$user->username.' Live Broadcating Started';
                $notification['u_id']           = @$request->get('u_id');
                $notification['type'] = 'Live Broadcating Started';
                $notification['body'] = @$user->username.' has been broadcasting Started.';
                $notification['notification_type'] = 'live_broadcasting';
                $notification['sender_id'] = @$user->id;
                $notification['title'] = 'Live Broadcating';
                $notification['sound'] = 'default';
                $user_data['notification'] = Event::dispatch('send-notification-assigned-user',array($user_follow,$notification));

                LiveNotification::create([
                    'user_id' => $user->id,
                    'follow_user' => $user_follow->id,
                    'u_id' => $request->get('u_id'),
                    'status' => 'pending',
                ]);
                Notifications::Create([
                    'user_id' => $user->id,
                    'follow_user' => $user_follow->id,
                    'title'   => 'Live Streaming',
                    'description' => @$user->username.' Started Live Broadacasting..!',
                    'flag_status' => 'live',
                    'channel_id' => @$request->get('u_id')
                     ]);
                }
            }
        } else {
            LiveNotification::create([
                'user_id' => $user->id,
                // 'follow_user' => $user_follow->id,
                'u_id' => $request->get('u_id'),
                'status' => 'pending',
            ]);
        }

        // $CheckFilterStatus = LiveNotification::where('u_id', $request->u_id)->where('user_id',$user->id)->get();
        // dd($CheckFilterStatus);

        LiveNotification::where('u_id', $request->u_id)->where('user_id',$user->id)->update(['filters' => $request->filters]);


        Log::info(json_encode($all_following));
        return response()->json(['status'=>'success','message'=>'Live Broadacasting Started Successfully Done','data' => $all_following]);
            } catch (Exception $e) {

        return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for User live Total Count
    -------------------------------------------------------------------------------------------- */
public function LiveCountUser(Request $request){
        $validator = [
            'u_id' =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }

            $totalGift = GiftDiamond::where('unique_id', $request->u_id)->where('receive_id',$user->id)->latest()->first();
            if(!empty($totalGift)){
                $gift_diamond = (int)$totalGift->gift_diamond;
            } else {
                $gift_diamond = (int)0;
            }

            $totalgiftdata = GiftDiamond::where('unique_id', $request->u_id)->get();
            if (!empty($totalgiftdata)) {
                if($request->gift_id){
                    $totalgiftdata = GiftDiamond::where('unique_id', $request->u_id)->where('id','>',$request->gift_id)->get();
                } else {
                    $totalgiftdata = GiftDiamond::where('unique_id', $request->u_id)->get();
                }
                 foreach ($totalgiftdata as $key => $value) {
                    if (!empty($value['gift_id'])) {
                        $totalgiftdata2 = Gift::where('id', $value->gift_id)->first();
                        $totalgiftdata[$key]['avatar'] = $totalgiftdata2->avatar;
                        $totalgiftdata[$key]['name'] = $totalgiftdata2->name;
                        $totalgiftdata[$key]['price'] = $totalgiftdata2->price;
                        $totalgiftdata[$key]['status'] = $totalgiftdata2->status;
                    }
                }
            }

            $totalusercount = LiveNotification::where('u_id', $request->u_id)->where('status', "online")->count();
            // dd($totalusercount);
            $total_chat_flag = LiveNotification::where('u_id', $request->u_id)->first();
            $total_control_buttons_flag = LiveNotification::where('u_id', $request->u_id)->first();
            $totaldata = LiveComment::where('u_id', $request->u_id)->whereIn('status', ["comment","join"])->get();
            $requestdata = LiveNotification::where('u_id', $request->u_id)->where('follow_user',$user->id)->whereIn('dual_status', ["accepted","rejected","requested"])->first();
            if(!empty($requestdata)){
            if($requestdata->dual_status == "accepted"){
                $data = "accepted";
            } elseif ($requestdata->dual_status == "rejected") {
                $data = "rejected";
            } elseif ($requestdata->dual_status == "requested") {
                $data = "requested";
            } else {
                $data = "";
            }
        } else {
            $data = "";
        }
            if (!empty($totaldata)) {
                if($request->comment_id){
                    $totaldata = LiveComment::with(['UserliveData'])->where('u_id', $request->u_id)->whereIn('status', ["comment","join"])->where('id','>',$request->comment_id)->get();
                } else {
                    $totaldata = LiveComment::with(['UserliveData'])->where('u_id', $request->u_id)->whereIn('status', ["comment","join"])->get();

            }
                 foreach ($totaldata as $key => $value) {
                    if (!empty($value['UserliveData'])) {
                        $totaldata [$key]['username'] = $value->UserliveData->username;
                        $totaldata [$key]['avatar'] = $value->UserliveData->avatar;
                    }
                    unset($totaldata[$key]['UserliveData']);
                }
            }
            if(!empty($totaldata)){
                $totaldata = $totaldata;
            }else{
                $totaldata= [];
            }
            Log::info(json_encode((int)$totalusercount));
            Log::info(json_encode($data));
            Log::info(json_encode($total_chat_flag->chat_flag));
            Log::info(json_encode($total_control_buttons_flag->control_buttons));
            Log::info(json_encode($totaldata));
            Log::info(json_encode($totalgiftdata));
            return response()->json(['status'=>'success','message'=>'User in Live and Comments Broadacasting..!', 'usercount'=>(int)$totalusercount, 'dual_flag'=>$data,'chat_flag'=>$total_chat_flag->chat_flag,'control_buttons_flag'=>$total_control_buttons_flag->control_buttons,'comments'=>$totaldata,'gifts'=>$totalgiftdata]);
            } catch (Exception $e) {
                return response()->json(['status' => 'error','message' => $e->getMessage()]);
            }
        }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Host live Total Count
    -------------------------------------------------------------------------------------------- */
    public function LiveCountHost(Request $request){
        $validator = [
            'u_id' =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $totalGift = GiftDiamond::where('unique_id', $request->u_id)->where('receive_id',$user->id)->latest()->first();

            if(!empty($totalGift)){
                $gift_diamond = (int)$totalGift->gift_diamond;
            } else {
                $gift_diamond = (int)0;
            }

            $totalgiftdata = GiftDiamond::where('unique_id', $request->u_id)->get();
            if (!empty($totalgiftdata)) {
                if($request->gift_id){
                    $totalgiftdata = GiftDiamond::where('unique_id', $request->u_id)->where('id','>',$request->gift_id)->get();
                } else {
                    $totalgiftdata = GiftDiamond::where('unique_id', $request->u_id)->get();
                }
                 foreach ($totalgiftdata as $key => $value) {
                    if (!empty($value['gift_id'])) {
                        $totalgiftdata2 = Gift::where('id', $value->gift_id)->first();
                        $totalgiftdata[$key]['avatar'] = $totalgiftdata2->avatar;
                        $totalgiftdata[$key]['name'] = $totalgiftdata2->name;
                        $totalgiftdata[$key]['price'] = $totalgiftdata2->price;
                        $totalgiftdata[$key]['status'] = $totalgiftdata2->status;
                    }
                }
            }

            $totalusercount = LiveNotification::where('u_id', $request->u_id)->where('status', "online")->count();
            
            $totalrequestcount = LiveNotification::where('u_id', $request->u_id)->where('dual_status', "requested")->count();
            $total_chat_flag = LiveNotification::where('u_id', $request->u_id)->first();
            $total_control_buttons_flag = LiveNotification::where('u_id', $request->u_id)->first();
            $totaldata = LiveComment::where('u_id', $request->u_id)->get();
            if (!empty($totaldata)) {
                if($request->comment_id){
                    $totaldata = LiveComment::with(['UserliveData'])->where('u_id', $request->u_id)->where('id','>',$request->comment_id)->get();
                } else {
                    $totaldata = LiveComment::with(['UserliveData'])->where('u_id', $request->u_id)->get();
                }
                 foreach ($totaldata as $key => $value) {
                    if (!empty($value['UserliveData'])) {
                        $totaldata [$key]['username'] = $value->UserliveData->username;
                        $totaldata [$key]['avatar'] = $value->UserliveData->avatar;
                    }
                    unset($totaldata[$key]['UserliveData']);
                }
            }

            if(!empty($totaldata)){
                $totaldata = $totaldata;
            }else{
                $totaldata= [];
            }
            Log::info(json_encode((int)$totalusercount));
            Log::info(json_encode((int)$totalrequestcount));
            Log::info(json_encode($total_chat_flag->chat_flag));
            Log::info(json_encode($total_control_buttons_flag->control_buttons));
            Log::info(json_encode($totaldata));
            Log::info(json_encode($gift_diamond));
            Log::info(json_encode($totalgiftdata));
            
            return response()->json(['status'=>'success','message'=>'User in Live and Comments Broadacasting..!', 'usercount'=> (int)$totalusercount,'requestedcount'=> (int)$totalrequestcount,'chat_flag'=>$total_chat_flag->chat_flag,'control_buttons_flag'=>$total_control_buttons_flag->control_buttons,'requested'=>$totaldata, 'gift_diamond'=>$gift_diamond,'gifts'=>$totalgiftdata]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

        /* -----------------------------------------------------------------------------------------
    @Description: Function for Win Diamond
    -------------------------------------------------------------------------------------------- */   
    public function LiveDiamondCount(Request $request){
        $validator = Validator::make($request->all(),[
        'u_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','message' => $validator->messages()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }

            $DiamondUser = GiftDiamond::where('unique_id',$request->u_id)->orderBy('id','desc')->groupby('receive_id')->pluck('receive_id');
            // dd($DiamondUser);
            $LiveDiamond = User::whereIn('id',$DiamondUser)->groupBy('id')->get()->toArray();
            // dd($LiveDiamond);
                foreach ($LiveDiamond as $key => $value) {

                    $check_gift = GiftDiamond::where('unique_id',$request->u_id)->where('receive_id',$value['id'])->sum('gift_diamond');
                    $LiveDiamond[$key]['Live_diamond'] = (string)$check_gift;
                    
                }
                Log::info(json_encode($LiveDiamond));
            return response()->json(['status'=>'success','message'=>"Live Diamonds Count",'Live_Diamond' => $LiveDiamond]);


        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Create Comment Live
    -------------------------------------------------------------------------------------------- */

    public function CreateCommentLive(Request $request){
        $validator = Validator::make($request->all(),[
        'u_id' => 'required',
        'user_id' => 'required',
        'comment' => 'required',
    ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','message' => $validator->messages()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            LiveComment::create([
                'u_id' => $request->u_id,
                'user_id' => $request->user_id,
                'follow_user' => $user->id,
                'status' => 'comment',
                'comment' => $request->comment,
            ]);
            return response()->json(['status' => 'success','message' => 'Comment List']);
        }catch (\Exception $exception){
            return response()->json(['status' => 'error','message' => $exception->getMessage()]);
        }
    }

   /* -----------------------------------------------------------------------------------------
    @Description: Function for User live Details
    -------------------------------------------------------------------------------------------- */
        public function LiveUserDetail(Request $request){
            $validation_array =[
                'u_id' => 'required',
            ];
            $validation = Validator::make($request->all(),$validation_array);
            if($validation->fails()){
                return response()->json(['status' => 'error','message' => $validation->messages()->first()],200);
            }
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                    return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
                }
                $use = LiveNotification::where('u_id', $request->u_id)->first();
                $UserDetail = User::with([
                    'follow_unfollow_flag' => function($query) use($user){
                        $query->where('followed_user_id', $user->id);
                    }])->where('id',$use->user_id)->first();

                if ($UserDetail->follow_unfollow_flag->count() > 0) {
                    $UserDetail['follow_flge'] = '1';
                } else {
                    $UserDetail['follow_flge'] = '0';
                }
                $data['User_data'] = $UserDetail;
                unset($UserDetail['follow_unfollow_flag']);

                Log::info(json_encode($UserDetail));
                return response()->json(['status' => 'success','message' => 'Live User Detail Successfully', 'data' => $UserDetail]);
            }catch (\Exception $e) {
                return response()->json(['status' => 'error','message' => $e->getMessage()]);
            }
        }
    /* -----------------------------------------------------------------------------------------
    @Description: Function for Status of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */

    public function LiveStreamingStatus(Request $request){
        $validator = [
        'u_id' =>'required',
        ];
            $validation = Validator::make($request->all(),$validator);
            if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
            }
                try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
                }
                $checkFilterStatus = LiveNotification::where('u_id', $request->u_id)->first();
                $LiveNotificationStatus = LiveNotification::where('follow_user', $user->id)->where('u_id', $request->u_id)->first();
                if(!empty($LiveNotificationStatus)){

                $joincommentstatus = LiveComment::where('follow_user', $user->id)->where('u_id', $request->u_id)->where('status',["join"])->first();
                if(empty($joincommentstatus)){
                    LiveComment::create([
                        'u_id' => $request->u_id,
                        'user_id' => $request->user_id,
                        'follow_user' => $user->id,
                        'status' => 'join',
                        'comment' => @$user->username.' has joined.',
                        ]);
                }

                LiveNotification::where('id', $LiveNotificationStatus->id)->update(['status' => "online",'viewer' =>'1']);

                return response()->json(['status'=>'success','message'=>'Live Broadacasting Joined..!','Filter_Flag'=>@$checkFilterStatus->filters]);    
                }
                else{

                $joincommentstatus = LiveComment::where('follow_user', $user->id)->where('u_id', $request->u_id)->where('status',["join"])->first();
                if(empty($joincommentstatus)){
                    LiveComment::create([
                        'u_id' => $request->u_id,
                        'user_id' => $request->user_id,
                        'follow_user' => $user->id,
                        'status' => 'join',
                        'comment' => @$user->username.' has joined.',
                        ]);
                }
                $hostcheck = User::where('id',$request->user_id)->first();
                    LiveNotification::create([
                    'user_id' => $request->get('user_id'),
                    'follow_user' => $user->id,
                    'u_id' => $request->get('u_id'),
                    'status' => 'online',
                    'filters' => @$checkFilterStatus->filters,
                    'viewer' =>'1',
                ]);

                Notifications::Create([
                    'user_id' => $user->id,
                    'follow_user' => $request->get('user_id'),
                    'title'   => 'Live Streaming',
                    'description' => ' Started Live Broadacasting..!',
                    'flag_status' => 'live',
                    'channel_id' => @$request->get('u_id')
                     ]);
                }
                Log::info(json_encode(@$checkFilterStatus->filters));
                return response()->json(['status'=>'success','message'=>'Live Broadacasting Joined..!','Filter_Flag'=>@$checkFilterStatus->filters]);
                
            } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Status of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */

    public function LiveStreamingStatusUserOffline(Request $request){
        $validator = [
        'u_id' =>'required',
        ];
            $validation = Validator::make($request->all(),$validator);
            if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
            }
                try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
                }
                $LiveNotificationStatus = LiveNotification::where('follow_user', $user->id)->where('u_id', $request->u_id)->first();
                LiveNotification::where('id', $LiveNotificationStatus->id)->update(['status' => "offline"]);
                
                return response()->json(['status'=>'success','message'=>'User Live Broadacasting Left..!']);
            } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Status of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */

    public function LiveStreamingStatusOffline(Request $request){
        $validator = [
        'u_id' =>'required',
        ];
            $validation = Validator::make($request->all(),$validator);
            if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
            }
                try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
                }
                $LiveNotificationStatus = LiveNotification::where('user_id', $user->id)->where('u_id', $request->u_id)->get();

                if ($LiveNotificationStatus->count()>0 ) {
                    foreach ($LiveNotificationStatus as $key => $value) {
                    LiveNotification::where('id', $value->id)->update(['status' => "offline"]);
                    Notifications::where('user_id',$user->id)->update(['flag_status' => "offline"]);
                    Notifications::where('channel_id',$value->u_id)->update(['flag_status' => 'offline']);
                }
            }
            // User::where('id',$user->id)->update(['available_flag' => 'offline']);
            return response()->json(['status'=>'success','message'=>'Host Live Broadacasting Ended..!']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }    
    
    /* -----------------------------------------------------------------------------------------
    @Description: Function for Status of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */

    public function useronlineoffline(){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            // User::where('id',$user->id)->update(['available_flag' => 'offline']);
            return response()->json(['status'=>'success','message'=>'You are offline']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }    
    
    /* -----------------------------------------------------------------------------------------
    @Description: Function for Add/Gift Diamond of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */
    public function addGiftDiamonds(Request $request){
        $validator = Validator::make($request->all(),[
            'sender_id' => 'required',
            'receive_id' => 'required',
            'gift_diamond' => 'required',
            'unique_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','message' => $validator->messages()->first()]);
        }
        try {
            $receiveUser = User::where('id',$request->receive_id)->first();
            $senderUser = User::where('id',$request->sender_id)->first();
            if(!empty($senderUser->diamond) && ((int)$senderUser->diamond >= (int)$request->gift_diamond)){
                $giftDiamond = GiftDiamond::Create([
                    "sender_id" =>request('sender_id'),
                    "receive_id" =>request('receive_id'),
                    // "gift_id"  =>request('gift_id'),
                    "gift_diamond" =>request('gift_diamond'),
                    "unique_id" =>request('unique_id'),
                ]);

                // user receive diamonds
                if(!empty($receiveUser) && (int)$receiveUser->diamond){
                    $receiveDiamond = (int)$receiveUser->diamond + (int)request('gift_diamond');
                    User::where('id',$request->receive_id)->update([ 'diamond' => $receiveDiamond]);
                }else{
                    User::where('id',$request->receive_id)->update([ 'diamond' => (int)request('gift_diamond')]);
                }

                // user sender diamonds
                if((int)$senderUser->diamond){
                    $senderDiamond = (int)$senderUser->diamond - (int)request('gift_diamond');
                    User::where('id',$request->sender_id)->update([ 'diamond' => $senderDiamond]);
                }else{
                    User::where('id',$request->sender_id)->update([ 'diamond' => (int)request('gift_diamond')]);
                }
                return response()->json(['status' => 'success','message' => "You have Successfully Sent ".request('gift_diamond')." Diamonds"]);
            } else {
                return response()->json(['status' => 'error','message' => 'Your wallet has not sufficient Gift Amount']);
            }
        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong....."],200, );
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Status of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */
    
    public function GiftDiamondsList(Request $request){

            $validator = Validator::make($request->all(),
                [
                    'receive_id' => 'required',
                    'unique_id' => 'required',
                ]);
            if($validator->fails()){
                return response()->json(['status' => 'error','message' => $validator->messages()->first()]);
            }
            try {
                $receiveUser = GiftDiamond::join('users', 'users.id', '=', 'gift_diamonds.sender_id')
                ->where('gift_diamonds.receive_id',$request->receive_id)
                ->where('gift_diamonds.unique_id',$request->unique_id)
                ->get();

                if($receiveUser->count() > 0){

                    Log::info(json_encode($receiveUser));
                    return response()->json(['status' => 'success','message' => "success", 'data'=>$receiveUser]);
                }else{
                    return response()->json(['status' => 'error','message' => "No Gift success"]);
                }
            }catch (Exception $e) {
                return response()->json(['status' => 'error','message' => "Something went Wrong....."],200, );
            }
        }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Dual Create Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */

       public function DualCreateLiveStreaming(Request $request)
            {
            $validator = [
            'u_id' =>'required',
            'follow_user' =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $all_follower = LiveNotification::where('follow_user', $user->id)->where('u_id',$request->u_id)->first();

            if(!empty($all_follower)){
                $user_first = User::find($all_follower->id);
                $notification['message'] = @$user->username.' Dual live Broadcating Joining';
                $notification['u_id'] = @$request->get('u_id');
                $notification['type'] = 'Live Broadcating Request';
                $notification['body'] = @$user->username.' has requested to dual the live.';
                $notification['notification_type'] = 'request_dual_broadcasting';
                $notification['title'] = 'Dual Broadcating';
                $notification['sound'] = 'default';
                $user_data['notification'] = Event::dispatch('send-notification-assigned-user',array($user_first,$notification));

                Notifications::Create([
                    'user_id' => $user->id,
                    'title'   => 'Dual Live Streaming',
                    'description' => @$user->username.' has Requested Dual Live Broadacasting..!',
                ]);
            }
            $DualLiveNotificationStatus = LiveNotification::where('follow_user', $user->id)->where('u_id', $request->u_id)->first();
            LiveNotification::where('id', $DualLiveNotificationStatus->id)->update(['dual_status' => "requested"]);

            $dualrequestcheck = LiveComment::where('follow_user', $user->id)->where('u_id', $request->u_id)->where('status',["requested"])->first();
            if(empty($dualrequestcheck)){
                LiveComment::create([
                    'u_id' => $request->u_id,
                    'user_id' => $DualLiveNotificationStatus->user_id,
                    'follow_user' => $user->id,
                    'status' => 'requested',
                    ]);
            }

            Log::info(json_encode($all_follower));
            return response()->json(['status'=>'success','message'=>'Dual Live Broadacasting Request Been Sent','data' => $all_follower]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Dual Status of Live Streaming Notifiction
    -------------------------------------------------------------------------------------------- */
   
    public function DualLiveStreamingStatus(Request $request){
        $validator = [
            'u_id' =>'required',
            'dual_status' =>'required',
            'follow_user' => 'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $DualLive_Check = LiveNotification::where('u_id', $request->u_id)->where('dual_status','accepted')->count();
            if($DualLive_Check >= '7'){
                return response()->json(['status'=>'success','message'=>'You can Share live Streaming with Siven person..!']);
            } else {

            $DualLiveNotificationStatus = LiveNotification::where('follow_user', $request->follow_user)->where('u_id', $request->u_id)->first();
            LiveNotification::where('id', $DualLiveNotificationStatus->id)->update(['dual_status' => $request->dual_status]);

            return response()->json(['status'=>'success']);
            //return response()->json(['status'=>'success','message'=>'Dual Live Broadacasting Status Updated..!']);
            }            
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


        /* -----------------------------------------------------------------------------------------
    @Description: Function for Chat And Control Button Hiden and Show
    -------------------------------------------------------------------------------------------- */
    public function ChatHidenAndShow(Request $request){
        $validator = [
            'u_id' =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            if($request->chat_flag){
            LiveNotification::where('u_id', $request->u_id)->update(['chat_flag' => $request->chat_flag]);
            }elseif($request->control_buttons){
                LiveNotification::where('u_id', $request->u_id)->update(['control_buttons' => $request->control_buttons]);
            }
            return response()->json(['status'=>'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Live Streamers Search And Follow User
    -------------------------------------------------------------------------------------------- */
        public function LiveStreamersSearch(Request $request){
            try {
                $user = JWTAuth::parseToken()->authenticate();
                if(!$user){
                    return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
                }
                if($request->serachingValue){
                    $all_followers = User::where('id','!=',$user->id)->where('username', 'like', "%{$request->get('serachingValue')}%")->where('user_type','!=','superadmin')->orderBy('id','ASC')->get()->toArray();
                    if(sizeof($all_followers) > 0){
                        $msg = "User Search Successfully Done..!";
                    } else{
                        $msg = "User Not Search Successfully Done..!";
                    }
                    // dd($all_followers);
                } else {
                    $FollowUser = FollowUser::where('user_id', $user->id)->pluck('followed_user_id');
                    $all_followers = User::where('id','!=',$user->id)->whereIn('id', $FollowUser)->orderBy('id','ASC')->get()->toArray();
                    if(sizeof($all_followers) > 0){
                        $msg = "User Followers List Successfully Done..!";
                    } else{
                        $msg = "Not Followers User List Successfully Done..!";
                    }
                }

                Log::info(json_encode($msg));
                Log::info(json_encode($all_followers));
                return response()->json(['status'=>'success','message'=>$msg,'data' => $all_followers]);
            } catch (Exception $e) {
                return response()->json(['status' => 'error','message' => $e->getMessage()]);
            }
        }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Live Streamers Dual
    -------------------------------------------------------------------------------------------- */
    public function LiveDualList(Request $request){
        $validator = [
            'u_id' =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $livelist = LiveNotification::where('u_id', $request->u_id)->first();
            $LiveHost = User::where('id', $livelist->user_id)->get()->toArray();

            $livelistDual = LiveNotification::where('u_id', $request->u_id)->where('dual_status','accepted')->pluck('follow_user');
            $Duallivelist = User::whereIn('id',$livelistDual)->get()->toArray();
            $data=array_merge($LiveHost,$Duallivelist);
            array_multisort( array_column($data, "id"), SORT_ASC, $data);

            Log::info(json_encode($data));    
            return response()->json(['status'=>'success','message'=>'List Dual And Host','data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Statistics
    -------------------------------------------------------------------------------------------- */   

    public function Statistics(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            if($request->statistics == "Year"){
            // dd("year");
                $timestemp = \Carbon\Carbon::today();
                $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;
                $FollowUser_count = FollowUser::where('user_id',$user->id)->whereYear('created_at','>=',$date)->count();
                $GiftDiamond = GiftDiamond::where('receive_id',$user->id)->whereYear('created_at','>=',$date)->get();
                $GiftDiamond_count = 0;
                foreach ($GiftDiamond as $value) {
                    $GiftDiamond_count+= $value->gift_diamond;
                }
                $LiveNotification_count = LiveNotification::where('user_id',$user->id)->whereYear('created_at','>=',$date)->where('viewer','1')->count();
                $msg = "Year Statistics List";
            } else {
                if($request->statistics == "Week"){
                // Month
                    $date = \Carbon\Carbon::today()->subDays(7);
                    $msg = "Week Statistics List";
                } elseif($request->statistics == "Month"){
                // Month
                    $date = \Carbon\Carbon::today()->startOfMonth();
                    $msg = "Month Statistics List";
                } else {
                // Week
                    $date = \Carbon\Carbon::today();
                    $msg = "Today Statistics List";
                }
                $FollowUser_count = FollowUser::where('user_id',$user->id)->where('created_at','>=',$date)->count();
                $GiftDiamond = GiftDiamond::where('receive_id',$user->id)->where('created_at','>=',$date)->get();
                $GiftDiamond_count = 0;
                foreach ($GiftDiamond as $value) {
                    $GiftDiamond_count+= $value->gift_diamond;
                }
                $LiveNotification_count = LiveNotification::where('user_id',$user->id)->where('created_at','>=',$date)->where('viewer','1')->count();
            }

            Log::info(json_encode($msg));
            Log::info(json_encode($FollowUser_count));
            Log::info(json_encode($GiftDiamond_count));
            Log::info(json_encode($LiveNotification_count));
            return response()->json(['status'=>'success','message'=>$msg,'FollowUser_count' => $FollowUser_count,'GiftDiamond_count' => $GiftDiamond_count, 'LiveNotification_count' => $LiveNotification_count]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Profile Section
    -------------------------------------------------------------------------------------------- */   
    public function ProfileSection (Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            if($request->Profiledata == "Year"){
                
                $timestemp = \Carbon\Carbon::today();
                $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;

                $checkblockuser = Helper::CheckBlockUser($user);
                $sendcheckblockuser = Helper::SendCheckBlockUser($user);
                $livenotification = LiveNotification::where('user_id',$user->id)->whereYear('created_at','>=',$date)->where('viewer','1')->count();
                $giftdiamond = User::where('id',$user->id)->first();
                $GiftDiamond_count = (int)$giftdiamond->diamond;


                $sender = GiftDiamond::with('receive_detail')->where('sender_id',$user->id)->whereYear('created_at','>=',$date)->groupby('receive_id')->get();

                foreach ($sender as $key => $value) {
                    $check_gift_sent = GiftDiamond::with('receive_detail')->where('receive_id',$value->receive_id)->where('sender_id',$user->id)->whereYear('created_at','>=',$date)->sum('gift_diamond');
                    $sender[$key]['sent_diamond'] = (string)$check_gift_sent;
                }

                $receive = GiftDiamond::with('send_detail')->where('receive_id',$user->id)->whereYear('created_at','>=',$date)->groupby('sender_id')->get();

                foreach ($receive as $key => $value) {
                    $check_gift_receive = GiftDiamond::with('send_detail')->where('sender_id',$value->sender_id)->where('receive_id',$user->id)->whereYear('created_at','>=',$date)->sum('gift_diamond');
                    $receive[$key]['receive_diamond'] = (string)$check_gift_receive;
                }
                
                $checkUserLive = LiveNotification::where('user_id','!=',$user->id)->whereYear('created_at','>=',$date)->groupBy('user_id')->pluck('user_id');
                
                Log::info(json_encode($GiftDiamond_count));
                Log::info(json_encode($livenotification));
                Log::info(json_encode($sender));
                Log::info(json_encode($receive));
                return response()->json(['status'=>'success','message'=>"Profile Section Year Successfully",'GiftDiamond_count' => $GiftDiamond_count,'LiveNotificationViewer_Count' => $livenotification, 'Sender' => $sender, 'receive'=> $receive]);
            }else{
                // dd("out");
                if($request->Profiledata == "Week"){
                // Month
                    $date = \Carbon\Carbon::today()->subDays(7);
                    $msg = "Week Profile Section";
                } elseif($request->Profiledata == "Month"){
                // Month
                    $date = \Carbon\Carbon::today()->startOfMonth();
                    $msg = "Month Profile Section";
                } else {
                // Week
                    $date = \Carbon\Carbon::today();
                    $msg = "Today Profile Section";
                }

                $checkblockuser = Helper::CheckBlockUser($user);
                $sendcheckblockuser = Helper::SendCheckBlockUser($user);
                $livenotification = LiveNotification::where('user_id',$user->id)->where('created_at','>=',$date)->where('viewer','1')->count();
                $giftdiamond = User::where('id',$user->id)->first();
                $GiftDiamond_count = (int)$giftdiamond->diamond;
                // $sender = GiftDiamond::with('receive_detail')->where('sender_id',$user->id)->where('created_at','>=',$date)->groupby('receive_id')->get();

                // $receive = GiftDiamond::with('send_detail')->where('receive_id',$user->id)->where('created_at','>=',$date)->groupby('sender_id')->get();
                $sender = GiftDiamond::with('receive_detail')->where('sender_id',$user->id)->where('created_at','>=',$date)->groupby('receive_id')->get();

                foreach ($sender as $key => $value) {
                    $check_gift_sent = GiftDiamond::with('receive_detail')->where('receive_id',$value->receive_id)->where('sender_id',$user->id)->where('created_at','>=',$date)->sum('gift_diamond');
                    $sender[$key]['sent_diamond'] = (string)$check_gift_sent;
                }

                $receive = GiftDiamond::with('send_detail')->where('receive_id',$user->id)->where('created_at','>=',$date)->groupby('sender_id')->get();

                foreach ($receive as $key => $value) {
                    $check_gift_receive = GiftDiamond::with('send_detail')->where('sender_id',$value->sender_id)->where('receive_id',$user->id)->where('created_at','>=',$date)->sum('gift_diamond');
                    $receive[$key]['receive_diamond'] = (string)$check_gift_receive;
                }
                // dd($receive);
                $checkUserLive = LiveNotification::where('user_id','!=',$user->id)->where('created_at','>=',$date)->groupBy('user_id')->pluck('user_id');

                Log::info(json_encode($GiftDiamond_count));
                Log::info(json_encode($livenotification));
                Log::info(json_encode($sender));
                Log::info(json_encode($receive));
                
                return response()->json(['status'=>'success','message'=>$msg,'GiftDiamond_count' => $GiftDiamond_count,'LiveNotificationViewer_Count' => $livenotification, 'Sender' => $sender, 'receive'=> $receive]);

            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Win Gift
    -------------------------------------------------------------------------------------------- */   
    public function WinGift(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $giftdiamond = GiftDiamond::where('receive_id',$user->id)->where('unique_id',$request->u_id)->get();
            $GiftDiamond_count = 0;
            foreach ($giftdiamond as $value) {
                $GiftDiamond_count+= $value->gift_diamond;
            }

            Log::info(json_encode($GiftDiamond_count));    
            return response()->json(['status'=>'success','message'=>"Win Gift Successfully",'GiftDiamond_count' => $GiftDiamond_count]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

        /* -----------------------------------------------------------------------------------------
    @Description: Function for Add/Gift Virtual Diamond of Live Streaming
    -------------------------------------------------------------------------------------------- */
    public function virtualGiftDiamonds(Request $request){
        $validator = Validator::make($request->all(),[
            'sender_id' => 'required',
            'receive_id' => 'required',
            'gift_id' => 'required',
            'unique_id' => 'required',
        ]);
        if($validator->fails()){
            return response()->json(['status' => 'error','message' => $validator->messages()->first()]);
        }

        $gift = Gift::where('id',$request->gift_id)->first();

        try {
            $receiveUser = User::where('id',$request->receive_id)->first();
            $senderUser = User::where('id',$request->sender_id)->first();
            $giftData = Gift::where('id',$request->gift_id)->first();

            if(!empty($senderUser->diamond) && ((int)$senderUser->diamond >= (int)$request->gift_id)){
                $giftDiamond = GiftDiamond::Create([
                    "sender_id" =>request('sender_id'),
                    "receive_id" =>request('receive_id'),
                    "gift_id" =>request('gift_id'),
                    "gift_diamond" =>@$gift->price,
                    "unique_id" =>request('unique_id'),
                ]);

                // user receive diamonds
                if(!empty($receiveUser) && (int)$receiveUser->diamond){
                    $receiveDiamond = (int)$receiveUser->diamond + (int)@$gift->price;
                    User::where('id',$request->receive_id)->update([ 'diamond' => $receiveDiamond]);
                }else{
                    User::where('id',$request->receive_id)->update([ 'diamond' => (int)@$gift->price]);
                }

                // user sender diamonds
                if((int)$senderUser->diamond){
                    $senderDiamond = (int)$senderUser->diamond - (int)@$gift->price;
                    User::where('id',$request->sender_id)->update([ 'diamond' => $senderDiamond]);
                }else{
                    User::where('id',$request->sender_id)->update([ 'diamond' => (int)@$gift->price]);
                }
                return response()->json(['status' => 'success','message' => "You have Successfully Sent ".request('gift_id')." Diamonds"]);
            } else {
                return response()->json(['status' => 'error','message' => 'Your wallet has not sufficient Gift Amount']);
            }
        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong....."],200, );
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Ads watch get Diamond
    -------------------------------------------------------------------------------------------- */
    public function adsgetdiamond(Request $request){
        $validator = [
            'user_id' =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status' => 'error','message' => $validation->errors()->first()]);
        }
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able from this application...'],200);
            }
            $rewarddiamond = Setting::where('code','rewarddiamond')->first();

            $rewardUser = User::where('id',$request->user_id)->first();
            if(!empty($rewardUser) && (int)$rewardUser->diamond){
                $rewardDiamond = (int)$rewardUser->diamond + $rewarddiamond->value;
                User::where('id',$request->user_id)->update([ 'diamond' => $rewardDiamond]);
            }else{
                User::where('id',$request->user_id)->update([ 'diamond' => $rewarddiamond->value]);
            }

            return response()->json(['status' => 'success','message' => "$rewarddiamond->value"]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }


    /* -----------------------------------------------------------------------------------------
    @Description: Function for Top Streamers Users
    -------------------------------------------------------------------------------------------- */
    public function topstreamingusers (Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $checkblockuser = Helper::CheckBlockUser($user);
            $sendcheckblockuser = Helper::SendCheckBlockUser($user);
            if($request->TopStreamer == 'Year'){
                $timestemp = \Carbon\Carbon::today();
                $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;

                $checkUserLive = DB::table('live_notification')->select('user_id',DB::raw('SUM(live_no) as live_no'))->groupby('user_id')->whereYear('created_at','>=',$date)->orderBy('live_no','desc')->pluck('user_id','live_no');

                $streamer_user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                    $query->where('followed_user_id', $user->id);
                }])->withPackageAmount()->WithStreamerCountYear()->whereIn('id',$checkUserLive)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->get()->toArray();

                foreach ($streamer_user_data as $key => $value) {
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $streamer_user_data[$key]['blockflag'] = '1';
                    } else {
                        $streamer_user_data[$key]['blockflag'] = '0';
                    }
                    if(!empty($user->id == $value['id'])){
                        $streamer_user_data[$key]['own_profile'] = '1';
                    } else {
                        $streamer_user_data[$key]['own_profile'] = '0';
                    }

                    if (!empty($value['follow_unfollow_flag'])) {
                        $streamer_user_data[$key]['follow_flge'] = '1';
                    } else {
                        $streamer_user_data[$key]['follow_flge'] = '0';
                    }
                    unset($streamer_user_data[$key]['follow_unfollow_flag']);
                    unset($streamer_user_data[$key]['live_streaming_flag']);
                }
                Log::info(json_encode($streamer_user_data));
                return response()->json(['status'=>'success','message'=>"Top Users of the Year",'Top_Streamers' => $streamer_user_data]);
            }else{

                if($request->TopStreamer == "Week"){

                    $timestemp = \Carbon\Carbon::today();
                    $date = \Carbon\Carbon::today()->subDays(7);
                    $msg = "Weeks Top Users Successfully";
                } elseif($request->TopStreamer == "Month"){

                    $timestemp = \Carbon\Carbon::today();
                    $date = \Carbon\Carbon::today()->startOfMonth();
                    $msg = "Months Top Users Successfully";
                } else {

                    $timestemp = \Carbon\Carbon::today();
                    $date = \Carbon\Carbon::today();
                    $msg = "Todays Top Users Successfully";
                }

                $checkUserLive = DB::table('live_notification')->select('user_id',DB::raw('SUM(live_no) as live_no'))->groupby('user_id')->where('created_at','>=',$date)->orderBy('live_no','desc')->pluck('user_id','live_no');

                $streamer_user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                    $query->where('followed_user_id', $user->id);
                }])->WithStreamerCountData()->whereIn('id',$checkUserLive)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->get()->toArray();

                foreach ($streamer_user_data as $key => $value) {
    
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $streamer_user_data[$key]['blockflag'] = '1';
                    } else {
                        $streamer_user_data[$key]['blockflag'] = '0';
                    }
                    if(!empty($user->id == $value['id'])){
                        $streamer_user_data[$key]['own_profile'] = '1';
                    } else {
                        $streamer_user_data[$key]['own_profile'] = '0';
                    }
                    if (!empty($value['follow_unfollow_flag'])) {
                        $streamer_user_data[$key]['follow_flge'] = '1';
                    } else {
                        $streamer_user_data[$key]['follow_flge'] = '0';
                    }
                    unset($streamer_user_data[$key]['follow_unfollow_flag']);
                    unset($streamer_user_data[$key]['live_streaming_flag']);
                }
                Log::info(json_encode($streamer_user_data));
                return response()->json(['status'=>'success','message'=>$msg,'Top_Streamers' => $streamer_user_data]);
            }
        }catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }



    /* -----------------------------------------------------------------------------------------
    @Description: Function for Top Gifters Users
    -------------------------------------------------------------------------------------------- */

    public function topgifterusers (Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $checkblockuser = Helper::CheckBlockUser($user);
            $sendcheckblockuser = Helper::SendCheckBlockUser($user);
            if($request->TopGifter == 'Year'){

            $timestemp = \Carbon\Carbon::today();
            $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;

            $sender = GiftDiamond::whereYear('created_at','>=',$date)->orderBy('id','desc')->groupby('sender_id')->pluck('sender_id');
            $gifter_user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                $query->where('followed_user_id', $user->id);
                }])->whereIn('id',$sender)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->groupBy('id')->get()->toArray();
                foreach ($gifter_user_data as $key => $value) {

                    $check_gift = GiftDiamond::where('sender_id',$value['id'])->whereYear('created_at','>=',$date)->sum('gift_diamond');
                    $gifter_user_data[$key]['sent_diamonds'] = (string)$check_gift;
                    
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $gifter_user_data[$key]['blockflag'] = '1';
                    } else {
                        $gifter_user_data[$key]['blockflag'] = '0';
                    }
                    if(!empty($user->id == $value['id'])){
                        $gifter_user_data[$key]['own_profile'] = '1';
                    } else {
                        $gifter_user_data[$key]['own_profile'] = '0';
                    }

                    if (!empty($value['follow_unfollow_flag'])) {
                        $gifter_user_data[$key]['follow_flge'] = '1';
                    } else {
                        $gifter_user_data[$key]['follow_flge'] = '0';
                    }

                    unset($gifter_user_data[$key]['follow_unfollow_flag']);
                    unset($gifter_user_data[$key]['live_streaming_flag']);
                }
                Log::info(json_encode($gifter_user_data));
            return response()->json(['status'=>'success','message'=>"Years Top Users Successfully",'Top_Gifters' => $gifter_user_data]);
        }else{
            if($request->TopGifter == 'Month'){
                $timestemp = \Carbon\Carbon::today();
                $date = \Carbon\Carbon::today()->startOfMonth();
                
                $sender = GiftDiamond::where('created_at','>=',$date)->groupby('sender_id')->pluck('sender_id');
    
                $gifter_user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                $query->where('followed_user_id', $user->id);
                }])->whereIn('id',$sender)->where('user_type','!=',$user->id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('id','DESC')->groupBy('id')->get()->toArray();
                foreach ($gifter_user_data as $key => $value) {

                    $check_gift = GiftDiamond::where('sender_id',$value['id'])->orderBy('gift_diamond')->where('created_at','>=',$date)->sum('gift_diamond');
                    $gifter_user_data[$key]['sent_diamonds'] = (string)$check_gift;
                    
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $gifter_user_data[$key]['blockflag'] = '1';
                    } else {
                        $gifter_user_data[$key]['blockflag'] = '0';
                    }
                    
                    if(!empty($user->id == $value['id'])){
                        $gifter_user_data[$key]['own_profile'] = '1';
                    } else {
                        $gifter_user_data[$key]['own_profile'] = '0';
                    }


                    if (!empty($value['follow_unfollow_flag'])) {
                        $gifter_user_data[$key]['follow_flge'] = '1';
                    } else {
                        $gifter_user_data[$key]['follow_flge'] = '0';
                    }

                    unset($gifter_user_data[$key]['follow_unfollow_flag']);
                    unset($gifter_user_data[$key]['live_streaming_flag']);
                }
                Log::info(json_encode($gifter_user_data));
                return response()->json(['status'=>'success','message'=>"Months Top Users Successfully",'Top_Gifters' => $gifter_user_data]);
            }elseif ($request->TopGifter == 'Week') {
                $timestemp = \Carbon\Carbon::today();
                $date = \Carbon\Carbon::today()->subDays(7);
        
                $sender = GiftDiamond::where('created_at','>=',$date)->groupby('sender_id')->pluck('sender_id');
                $gifter_user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                $query->where('followed_user_id', $user->id);
                }])->whereIn('id',$sender)->where('user_type','!=',$user->id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('id','DESC')->groupBy('id')->get()->toArray();
                foreach ($gifter_user_data as $key => $value) {

                    $check_gift = GiftDiamond::where('sender_id',$value['id'])->orderBy('gift_diamond')->where('created_at','>=',$date)->sum('gift_diamond');
                    $gifter_user_data[$key]['sent_diamonds'] = (string)$check_gift;
                    
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $gifter_user_data[$key]['blockflag'] = '1';
                    } else {
                        $gifter_user_data[$key]['blockflag'] = '0';
                    }
                    
                    if(!empty($user->id == $value['id'])){
                        $gifter_user_data[$key]['own_profile'] = '1';
                    } else {
                        $gifter_user_data[$key]['own_profile'] = '0';
                    }


                    if (!empty($value['follow_unfollow_flag'])) {
                        $gifter_user_data[$key]['follow_flge'] = '1';
                    } else {
                        $gifter_user_data[$key]['follow_flge'] = '0';
                    }

                    unset($gifter_user_data[$key]['follow_unfollow_flag']);
                    unset($gifter_user_data[$key]['live_streaming_flag']);
                }
                Log::info(json_encode($gifter_user_data));
                return response()->json(['status'=>'success','message'=>"Weeks Top Users Successfully",'Top_Gifters' => $gifter_user_data]);
            }else{
                $timestemp = \Carbon\Carbon::today();
                $date = \Carbon\Carbon::today();
                
                $sender = GiftDiamond::where('created_at','>=',$date)->groupby('sender_id')->pluck('sender_id');
                $gifter_user_data = User::with(['follow_unfollow_flag' => function($query) use($user){
                $query->where('followed_user_id', $user->id);
                }])->whereIn('id',$sender)->where('user_type','!=',$user->id)->whereNotIn('id',$checkblockuser)->whereNotIn('id',$sendcheckblockuser)->where('user_type','!=','superadmin')->orderBy('id','DESC')->groupBy('id')->get()->toArray();
                foreach ($gifter_user_data as $key => $value) {

                    $check_gift = GiftDiamond::where('sender_id',$value['id'])->orderBy('gift_diamond')->where('created_at','>=',$date)->sum('gift_diamond');
                    $gifter_user_data[$key]['sent_diamonds'] = (string)$check_gift;
                    
                    $blockflag_user = BlockUserList::where('user_id',$user->id)->first();
                    if(!empty($blockflag_user['id'] == $value['id'])){
                        $gifter_user_data[$key]['blockflag'] = '1';
                    } else {
                        $gifter_user_data[$key]['blockflag'] = '0';
                    }
                    
                    if(!empty($user->id == $value['id'])){
                        $gifter_user_data[$key]['own_profile'] = '1';
                    } else {
                        $gifter_user_data[$key]['own_profile'] = '0';
                    }


                    if (!empty($value['follow_unfollow_flag'])) {
                        $gifter_user_data[$key]['follow_flge'] = '1';
                    } else {
                        $gifter_user_data[$key]['follow_flge'] = '0';
                    }

                    unset($gifter_user_data[$key]['follow_unfollow_flag']);
                    unset($gifter_user_data[$key]['live_streaming_flag']);
                }
                Log::info(json_encode($gifter_user_data));
                return response()->json(['status'=>'success','message'=>"Todays Top Users Successfully",'Top_Gifters' => $gifter_user_data]);
            }
                
            }

        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | @Description: Function for Upload Video Ios
    |--------------------------------------------------------------------------
    */
    public function SavevideoIos(Request $request)
    {
        ini_set('max_execution_time', 9999999);
        set_time_limit(9999999);

        try {
            $validator = Validator::make($request->all(), [
                'u_id' => 'required',
                // 'picbase64'     => 'required|file|mimes:jpeg,jpg,png,gif|max:3000',
                // 'videobase64'   => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm,video/avi,video/mpeg,video/quicktime',
                'picbase64'     => 'required',
                'videobase64'   => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status'   => 'error','message'  => $validator->messages()->first()]);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }

            $thum = $request->picbase64;
            $video = $request->videobase64;
            $video_url = "";
            $thum_url = '';

            if ($request->hasFile('picbase64')) {
                $file = $request->file('picbase64');
                $uniqueid=uniqid();
                $original_name=$file->getClientOriginalName();
                $size=$file->getSize();
                $extension=$file->getClientOriginalExtension();
                $fileName=  rand()."_".rand().'.'.$extension;
                $file->move(public_path('/uploads/thum'), $fileName);
                $thum_url = "uploads/thum/".$fileName;
            }

            if ($request->hasFile('videobase64')) {
                $file = $request->file('videobase64');
                $uniqueid=uniqid();
                $original_name=$file->getClientOriginalName();
                $size=$file->getSize();
                $extension=$file->getClientOriginalExtension();
                $fileName=  rand()."_".rand().'.'.$extension;
                $file->move(public_path('/uploads/video'), $fileName);
                $video_url = "uploads/video/".$fileName;
            }

            Videos::create([
                'user_id' => @$user->id,
                'u_id' => @$request->u_id,
                'video' => @$video_url,
                'thum' => @$thum_url,
                'user_flag' => "yes",
                'admin_flag' => "no"
            ]);
            return response()->json(['status'=>'success','message'=>"Videos Upload Successfully !..."]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | @Description: Function for Upload Video Android
    |--------------------------------------------------------------------------
    */
    public function SavevideoAndroid(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $user_id = $user->id;
            $event_json = $request->toArray();
            ini_set('max_execution_time', 9999999);
            set_time_limit(9999999);
            if (isset($user_id) && isset($event_json['picbase64'])  && isset($event_json['videobase64'])) {
                $user_id=htmlspecialchars(strip_tags($user_id, ENT_QUOTES));
                $thum = $event_json['picbase64']['file_data'];
                $video = $event_json['videobase64']['file_data'];
                $fileName=rand()."_".rand();
                $video_url="uploads/video/".$fileName.".mp4";
                $thum_url="uploads/thum/".$fileName.".jpg";
                $thum = base64_decode($thum);
                file_put_contents("uploads/thum/".$fileName.".jpg", $thum);
                $filename = 'uploads/thum/'.$fileName.'.jpg';
                $percent = 0.4;
                $video = base64_decode($video);
                file_put_contents("uploads/video/".$fileName.".mp4", $video);
                $filename = 'uploads/thum/'.$fileName.'.jpg';
                Videos::create([
                    'video' => $video_url,
                    'u_id' => @$request->u_id,
                    'user_flag' => "yes",
                    'admin_flag' => "no",
                    'user_id' => $user_id,
                    'thum' => $thum_url
                    ]);
                return response()->json(['status'=>'success','message'=>"Videos Upload Successfully !..."]);
            } else {
                return response()->json(['status'   => 'error','message'  => 'Required parameter is missing !!']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | @Description: Function for Delete Videos
    |--------------------------------------------------------------------------
    */
    public function DeleteVideo(Request $request){
        $validator = [
            'video_id'   =>'required',
        ];
        $validation = Validator::make($request->all(),$validator);
        if($validation->fails()){
            return response()->json(['status'    => 'error','message'   => $validation->errors()->first()]);
        }
        try{
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }

            $video_data = Videos::where('user_id',$user->id)->where('id',request('video_id'))->first();
            if($video_data){
                $video_data->delete();
                return response()->json(['status'=>'success','message'=>'Video Deleted Successfully']);
            }else{
                return response()->json(['status'=>'error','message'=>'Error in updating']);
            }
        }catch(Exception $e){
            return response()->json(['status'    => 'error','message'   => $e->getMessage()]);
        }
    }

    /* -----------------------------------------------------------------------------------------
    @Description: Function for Get Own Details Profile
    -------------------------------------------------------------------------------------------- */

    public function detailsProfile(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if(!$user){
                return response()->json(['status'=>'error','message' => 'You are not able login from this application...'],200);
            }
            $user_data = User::where(['id'=>$user->id])->first();
            $liveveiwcount = LiveNotification::where('user_id',$user->id)->groupby('u_id')->get()->count('u_id');
            $giftdiamond = GiftDiamond::where('receive_id',$user->id)->sum('gift_diamond');
            
            $followercount = FollowUser::where('user_id',$user->id)->sum('status');
            $followingcount = FollowUser::where('followed_user_id',$user->id)->sum('status');
            
            $alllistfollowers = FollowUser::where('user_id',$user->id)->get();
            $data['user']  = $user_data;
            $follow['user']  = $alllistfollowers;

            Log::info(json_encode((string)$liveveiwcount));
            Log::info(json_encode((string)$followercount));
            Log::info(json_encode((string)$followingcount));
            Log::info(json_encode((string)$giftdiamond));
            Log::info(json_encode($user_data));
            Log::info(json_encode($alllistfollowers));

            return response()->json(['status' => 'success','message' => 'Detailed Profile Successfull','Live_View_Count' => (string)$liveveiwcount,'Followers_count' => (string)$followercount,'Following_count' => (string)$followingcount,'Earned_Diamonds' => (string)$giftdiamond,'data' => $user_data,'myfollower' => $alllistfollowers]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error','message' => "Something went Wrong..."],200);
        }
    }
}