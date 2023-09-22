<style>
    .messageWrap {margin-top: 10px; margin-bottom: 10px; background-color: #404e66; padding: 10px; border-radius: 25px; width: 300px;}
    .messageWrap .messageText {position: relative; display: flex; padding: 0; line-height: 1.6;}
    .messageWrap .messageHours {display: inline-block; position: relative; margin-left: 0px; margin-top: 8px; font-style: italic; color: #9E9E9E;}
    .messageWrap .messageAvatar img {width: 40px; height: 40px;float: left; position: relative; margin-right: 15px; border-radius: 50%;}
</style>
<div class="col-lg-12 col-md-12 col-sm-12 desk-p-left">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                @foreach($allMessages as $key)
                    @if((int)$key->sender_id !== (int)$userID)
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <div class="messageWrap">
                                <div class="messageAvatar">
                                    @if($key->userInfoFrom->avatar === '' || $key->userInfoFrom->avatar === 'default.png')
                                        <img src="{!! asset('storage/avatar/default.png') !!}" style="height: 30px; width: 30px; border-radius: 50%; border: 3px solid transparent; padding: 0px; box-shadow: 2px 2px 10px; background-color: #FFF;">
                                    @else
                                        <img src="{{ asset('storage/avatar/'.$key->userInfoFrom->avatar) }}" style="height: 30px; width: 30px; border-radius: 50%; border: 3px solid transparent; padding: 0px; box-shadow: 2px 2px 10px; background-color: #FFF;">
                                    @endif
                                </div>
                                <div class="messageText" style="color: #FFF;">{{ $key->message }} </div>
                                <div class="messageHours" style="font-size: 10px; color: #FFF;">
                                    {{ \Carbon\Carbon::createFromTimeStamp(strtotime($key->created_at))->diffForHumans() }} 
                                    <b>{{ $key->userInfoFrom->email }}</b>
                                </div>
                                
                            </div>
                        </div>
                    @else
                        <div class="col-xl-12 col-lg-12 col-sm-12 col-xs-12">
                            <div class="messageWrap" style="float: right;">
                                <div class="messageAvatar">
                                    @if($key->userInfoFrom->avatar === '' || $key->userInfoFrom->avatar === 'default.png')
                                        <img src="{!! asset('storage/avatar/default.png') !!}" style="height: 30px; width: 30px; border-radius: 50%; border: 3px solid transparent; padding: 0px; box-shadow: 2px 2px 10px; background-color: #FFF;">
                                    @else
                                        <img src="{{ asset('storage/avatar/'.$key->userInfoFrom->avatar) }}" style="height: 30px; width: 30px; border-radius: 50%; border: 3px solid transparent; padding: 0px; box-shadow: 2px 2px 10px; background-color: #FFF;">
                                    @endif
                                </div>
                                <div class="messageText" style="color: #FFF;">
                                    {{ $key->message }} 
                                </div>
                                <div class="messageHours" style="font-size: 10px; color: #FFF;">{{ \Carbon\Carbon::createFromTimeStamp(strtotime($key->created_at))->diffForHumans() }} <b>{{ $key->userInfoFrom->email }}</b></div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>