<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
URL::forceScheme('https');

Route::get('/about-us','HomeController@aboutus')->name('aboutus');


Route::group(['middleware' => 'preventBackHistory'],function(){
	Route::get('/','Admin\Auth\LoginController@showLoginForm')->name('admin.showLoginForm');
	Route::get('admin/login','Admin\Auth\LoginController@showLoginForm')->name('admin.login');
	Route::post('admin/login', 'Admin\Auth\LoginController@login');
	Route::get('admin/resetPassword','Admin\Auth\PasswordResetController@showPasswordRest')->name('admin.resetPassword');
	Route::post('admin/sendResetLinkEmail', 'Admin\Auth\ForgotPasswordController@sendResetLinkEmail')->name('admin.sendResetLinkEmail');
	Route::get('admin/find/{token}', 'Admin\Auth\PasswordResetController@find')->name('admin.find');
	Route::post('admin/create', 'Admin\Auth\PasswordResetController@create')->name('admin.sendLinkToUser');
	Route::post('admin/reset', 'Admin\Auth\PasswordResetController@reset')->name('admin.resetPassword_set');
	Route::group(['prefix' => 'admin','middleware'=>'Admin','namespace' => 'Admin','as' => 'admin.'],function(){
		Route::get('/dashboard','MainController@dashboard')->name('dashboard');
		Route::get('/logout','Auth\LoginController@logout')->name('logout');
		
		//====================> User Management =========================
		Route::get('/user','UsersController@index')->name('index');
		Route::get('/user/show','UsersController@show')->name('user.show');
		Route::get('/user/create','UsersController@create')->name('create');
		Route::post('/user/store','UsersController@store')->name('store');
		Route::get('/user/edit/{id}','UsersController@edit')->name('edit');
		Route::post('/user/update/{id}','UsersController@update')->name('update');
		Route::post('/user/delete/{id}','UsersController@delete')->name('delete');
		Route::post('/user/change_status','UsersController@change_status')->name('change_status');
		Route::get('/show/{sent_from}','UsersController@show')->name('show');
		//====================> Update Admin Profile =========================
		Route::get('/profile','UsersController@updateProfile')->name('profile');
		Route::post('/updateProfileDetail','UsersController@updateProfileDetail')->name('updateProfileDetail');
		Route::post('/updatePassword','UsersController@updatePassword')->name('updatePassword');
		//====================> CMS Management =========================
		Route::get('/cms','CmsController@index')->name('cms.index');
		Route::get('/cms/show','CmsController@show')->name('cms.show');
		Route::get('/cms/edit/{id}','CmsController@edit')->name('cms.edit');
		Route::post('/cms/update/{id}','CmsController@update')->name('cms.update');
		Route::post('/cms/delete/{id}','CmsController@delete')->name('cms.delete');
		Route::post('/cms/change_status','CmsController@change_status')->name('cms.change_status');
		//====================> Diamond Management =========================
		Route::get('/diamond','DiamondController@index')->name('diamond.index');
		Route::get('/diamond/create','DiamondController@create')->name('diamond.create');
		Route::post('/diamond/store','DiamondController@store')->name('diamond.store');
		Route::get('/diamond/show','DiamondController@show')->name('diamond.show');
		Route::get('/diamond/edit/{id}','DiamondController@edit')->name('diamond.edit');
		Route::post('/diamond/update/{id}','DiamondController@update')->name('diamond.update');
		Route::post('/diamond/delete/{id}','DiamondController@delete')->name('diamond.delete');
		Route::post('/diamond/change_status','DiamondController@change_status')->name('diamond.change_status');

		//====================> Banner Management =========================
		Route::get('/banner','BannerController@index')->name('banner.index');
		Route::get('/banner/create','BannerController@create')->name('banner.create');
		Route::post('/banner/store','BannerController@store')->name('banner.store');
		Route::get('/banner/show','BannerController@show')->name('banner.show');
		Route::get('/banner/edit/{id}','BannerController@edit')->name('banner.edit');
		Route::post('/banner/update/{id}','BannerController@update')->name('banner.update');
		Route::post('/banner/delete/{id}','BannerController@delete')->name('banner.delete');
		Route::post('/banner/status','BannerController@status')->name('banner.status');

		//====================> Report Category Management =========================
		Route::get('/report_category','ReportCategoryController@index')->name('report_category.index');
		Route::get('/report_category/create','ReportCategoryController@create')->name('report_category.create');
		Route::post('/report_category/store','ReportCategoryController@store')->name('report_category.store');
		Route::get('/report_category/show','ReportCategoryController@show')->name('report_category.show');
		Route::get('/report_category/edit/{id}','ReportCategoryController@edit')->name('report_category.edit');
		Route::post('/report_category/update/{id}','ReportCategoryController@update')->name('report_category.update');
		Route::post('/report_category/delete/{id}','ReportCategoryController@delete')->name('report_category.delete');
		Route::post('/report_category/change_status','ReportCategoryController@change_status')->name('report_category.change_status');

		//====================> Gift Management =========================
		Route::get('/gift','GiftController@index')->name('gift.index');
		Route::get('/gift/create','GiftController@create')->name('gift.create');
		Route::post('/gift/store','GiftController@store')->name('gift.store');
		Route::get('/gift/show','GiftController@show')->name('gift.show');
		Route::get('/gift/edit/{id}','GiftController@edit')->name('gift.edit');
		Route::post('/gift/update/{id}','GiftController@update')->name('gift.update');
		Route::post('/gift/delete/{id}','GiftController@delete')->name('gift.delete');
		Route::post('/gift/change_status','GiftController@change_status')->name('gift.change_status');

		//====================> Payment Management =========================
		Route::get('/transactions','PaymentController@index')->name('transactions.index');
		Route::get('/transactions/show','PaymentController@show')->name('transactions.show');
		
		//====================> Report User Management =========================
		Route::get('/report_user','ReportUserController@index')->name('report_user.index');
		Route::get('/report_user/show','ReportUserController@show')->name('report_user.show');
		Route::get('/report_user/edit/{id}','ReportUserController@edit')->name('report_user.edit');
		Route::post('/report_user/update/{id}','ReportUserController@update')->name('report_user.update');

		//====================> User Video Management =========================
		Route::get('/videos','VideoController@index')->name('videos.index');		
		Route::post('/videos/delete/{id}','VideoController@delete')->name('videos.delete');
		Route::post('/videos/change_status','VideoController@change_status')->name('videos.change_status');


		//====================> User Chat Management =========================
		Route::get('/conversations', 'ConversationsController@index')->name('conversations.index');
		Route::get('/conversations./show/{sender_id}','ConversationsController@show')->name('conversations.show');
		Route::get('/converstionList/','ConversationsController@converstionList')->name('conversations.converstionList');
	});
});


Event::listen('send-notification-assigned-user', function($value,$data) {
    try {

        $path = public_path().'/webservice_logs/'.date("d-m-Y").'_notification.log';
        file_put_contents($path, "\n\n".date("d-m-Y") . "_ : ".json_encode(['user'=>$value->id,'data'=>$data])."\n", FILE_APPEND);
        $response = [];
        $device_token = $value->device_token;
        if($value->device_type == 'ios'){
            try {
                $passphrase = '';
                $cert =config_path('iosCertificates/pushcert.pem');
                $message = json_encode($data);
                $ctx = stream_context_create();
                stream_context_set_option($ctx, 'ssl', 'local_cert', $cert);
                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,
                    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
                if (!$fp)
                    exit("Failed to connect: $err $errstr" . PHP_EOL);
                $body['aps'] = array('alert' => $data['message'],'sound' => 'default');
                $body['params'] = $data;
                $payload = json_encode($body);
                $msg = chr(0) . pack('n', 32) . pack('H*', $device_token) . pack('n', strlen($payload)) . $payload;
                $result = fwrite($fp, $msg, strlen($msg));
                fclose($fp);
                $response[] = $result;
                file_put_contents($path, "\n\n".date("d-m-Y") . "_Response_IOS payload : ".json_encode($payload)."\n", FILE_APPEND);
                file_put_contents($path, "\n\n".date("d-m-Y") . "_Response_IOS : ".json_encode(@$_Responsese)."\n", FILE_APPEND);
            } catch (Exception $e) {
                file_put_contents($path, "\n\n".date("d-m-Y") . "_Response_IOS : ".$e."\n", FILE_APPEND);
            }
        }else{
            file_put_contents($path, "\n\n".date("d-m-Y") . "_Notification_data : ".json_encode($data)."\n", FILE_APPEND);

            $response[] = PushNotification::setService('fcm')->setMessage([
                'data' => $data
                ])->setApiKey('AAAAU9VW3iA:APA91bEAs1WIOkxiDDWTH9fqP40os8Z5UmROCx7Z6v4sBa-btKUic9_cFGMCBZGTRLLdJg2QIs37vFHXTXZDaiBgng7_UzREJmXdg2YeMzu20dHGsklkTakWUNAsszJEdheMo-p6VHyu')->setConfig(['dry_run' => false])->sendByTopic($data['type'])->setDevicesToken([$device_token])->send()->getFeedback();
            }
            file_put_contents($path, "\n\n".date("d-m-Y") . "_Response_User_android : ".json_encode($response)."\n", FILE_APPEND);
            return $response;
        } catch (Exception $e) {
            file_put_contents($path, "\n\n".date("d-m-Y") . "_Response : ".json_encode($e)."\n", FILE_APPEND);
        }
    });
