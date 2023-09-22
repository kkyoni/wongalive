<?php

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
*/
URL::forceScheme('https');

Route::namespace('Api\User')->group(function () {
    Route::group(['middleware' => ['cors']], function() {
        Route::post('login','UserController@login');
        Route::post('register','UserController@register');
        Route::post('sendOtp','UserController@sendOtp');
        Route::post('verifyOtp','UserController@verifyOtp');
    });

    /*------------- JWT TOKEN AUTHORIZED ROUTE-------------------*/
    Route::group(['middleware' => ['cors','jwt.verify']], function() {
        Route::get('getProfile','UserController@getProfile')->middleware('activeUserCheck');
        Route::post('updateProfile','UserController@updateProfile')->middleware('activeUserCheck');
        Route::post('changePassword','UserController@changePassword')->middleware('activeUserCheck');
        Route::post('logout','UserController@logout')->middleware('activeUserCheck');
        Route::get('getNotifications','UserController@getNotifications')->middleware('activeUserCheck');
        Route::post('getUserDetail','UserController@getUserDetail')->middleware('activeUserCheck');
        Route::get('getDiamonds','UserController@getDiamonds')->middleware('activeUserCheck');
        Route::post('add_CardDetails','UserController@add_CardDetails')->middleware('activeUserCheck');
        Route::post('edit_CardDetails','UserController@edit_CardDetails')->middleware('activeUserCheck');
        Route::post('delete_CardDetails','UserController@delete_CardDetails')->middleware('activeUserCheck');
        Route::get('view_CardDetails','UserController@view_CardDetails')->middleware('activeUserCheck');
        Route::post('default_CardDetails','UserController@default_CardDetails')->middleware('activeUserCheck');
        Route::get('countDiamond','UserController@countDiamond')->middleware('activeUserCheck');
        Route::post('paymenthistory','UserController@paymenthistory')->middleware('activeUserCheck');
        Route::get('getpaymenthistory','UserController@getpaymenthistory')->middleware('activeUserCheck');
        Route::get('reportCategory','UserController@reportCategory')->middleware('activeUserCheck');        
        Route::post('createUserFollower','UserController@createUserFollower')->middleware('activeUserCheck');
        Route::post('unfollowUser','UserController@unfollowUser')->middleware('activeUserCheck');
        Route::get('getFollowers','UserController@getFollowers')->middleware('activeUserCheck');
        Route::get('getFollowing','UserController@getFollowing')->middleware('activeUserCheck');
        Route::post('CreateLiveStreaming','UserController@CreateLiveStreaming')->middleware('activeUserCheck');
        Route::post('LiveStreamingStatus','UserController@LiveStreamingStatus')->middleware('activeUserCheck');
        Route::post('LiveStreamingStatusUserOffline','UserController@LiveStreamingStatusUserOffline')->middleware('activeUserCheck');
        Route::post('LiveStreamingStatusOffline','UserController@LiveStreamingStatusOffline')->middleware('activeUserCheck');
        Route::post('addGiftDiamonds','UserController@addGiftDiamonds')->middleware('activeUserCheck');
        Route::post('GiftDiamondsList','UserController@GiftDiamondsList')->middleware('activeUserCheck');
        Route::post('LiveUserDetail','UserController@LiveUserDetail')->middleware('activeUserCheck');
        Route::post('readunreadNotifications','UserController@readunreadNotifications')->middleware('activeUserCheck');
        Route::get('useronlineoffline','UserController@useronlineoffline')->middleware('activeUserCheck');
        Route::post('LiveCountHost','UserController@LiveCountHost')->middleware('activeUserCheck');
        Route::post('DualCreateLiveStreaming','UserController@DualCreateLiveStreaming')->middleware('activeUserCheck');
        Route::post('DualLiveStreamingStatus','UserController@DualLiveStreamingStatus')->middleware('activeUserCheck');
        Route::post('LiveCountUser','UserController@LiveCountUser')->middleware('activeUserCheck');
        Route::post('CreateCommentLive','UserController@CreateCommentLive')->middleware('activeUserCheck');
        Route::post('reportUser','UserController@reportUser')->middleware('activeUserCheck');        
        Route::post('blockUserCreate','UserController@blockUserCreate')->middleware('activeUserCheck');
        Route::post('blockUserList','UserController@blockUserList')->middleware('activeUserCheck');
        Route::post('ChatHidenAndShow','UserController@ChatHidenAndShow')->middleware('activeUserCheck');
        Route::post('LiveStreamersSearch','UserController@LiveStreamersSearch')->middleware('activeUserCheck');
        Route::post('LiveDualList','UserController@LiveDualList')->middleware('activeUserCheck');
        Route::post('Statistics','UserController@Statistics')->middleware('activeUserCheck');
        Route::post('LiveSearchFollower','UserController@LiveSearchFollower')->middleware('activeUserCheck');
        Route::post('WinGift','UserController@WinGift')->middleware('activeUserCheck');
        Route::post('virtualGiftDiamonds','UserController@virtualGiftDiamonds')->middleware('activeUserCheck');
        Route::post('adsgetdiamond','UserController@adsgetdiamond')->middleware('activeUserCheck');
        Route::post('topstreamingusers','UserController@topstreamingusers')->middleware('activeUserCheck');
        Route::post('SavevideoIos','UserController@SavevideoIos')->middleware('activeUserCheck');
        Route::post('SavevideoAndroid','UserController@SavevideoAndroid')->middleware('activeUserCheck');
        Route::post('deleteVideo', 'UserController@deleteVideo')->middleware('activeUserCheck');
        Route::get('detailsProfile','UserController@detailsProfile')->middleware('activeUserCheck');
        Route::post('ProfileSection','UserController@ProfileSection')->middleware('activeUserCheck');
        Route::post('topgifterusers','UserController@topgifterusers')->middleware('activeUserCheck');

        //Working
        
        Route::post('LiveDiamondCount','UserController@LiveDiamondCount')->middleware('activeUserCheck');

    });
    
    /*-------------Without JWT TOKEN AUTHORIZED ROUTE-------------------*/
    });

    /*
    |--------------------------------------------------------------------------
    | COMMON API Routes
    |--------------------------------------------------------------------------
    |
    */
    Route::namespace('Api')->group(function () {
        Route::group(['middleware' => ['cors','jwt.verify']], function() {
            Route::post('homescreen', 'CommonController@homescreen')->middleware('activeUserCheck');
            Route::post('searchuser', 'CommonController@searchuser')->middleware('activeUserCheck');
            Route::get('getBanner','CommonController@getBanner')->middleware('activeUserCheck');
            Route::get('GetGift', 'CommonController@GetGift')->middleware('activeUserCheck');
            Route::post('updateToken','CommonController@updateToken');
        });

        Route::post('forgotPassword','CommonController@forgotPassword');
        Route::post('resetPassword','CommonController@resetPassword');
        Route::get('CmsPage','CommonController@CmsPage');
        Route::post('usersvideo', 'CommonController@usersvideo');

    });
