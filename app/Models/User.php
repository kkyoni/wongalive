<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Uuid;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table = 'users';
    protected $fillable = [
        'username','first_name', 'last_name', 'contact_number','email', 'password', 'user_type','status','avatar','gender','device_token','device_type','social_id','social_media','sign_up_as','link_code','otp_varifiy','notification','isReport','available_flag','diamond'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    'id'=>'int',
    'username' => 'string',
    'first_name' => 'string',
    'last_name' => 'string',
    'contact_number' => 'string',
    'email' => 'string',
    'user_type' => 'string',
    'status' => 'string',
    'avatar' => 'string',
    'gender' => 'string',
    'device_token' => 'string',
    'device_type' => 'string',
    'social_id' => 'string',
    'social_media' => 'string',
    'sign_up_as' => 'string',
    'link_code' => 'string',
    'otp_varifiy' => 'string',
    'total_diamonds' => 'integer',
    'follow_flge'=>'string',
    'notification'=>'string',
    'isReport'=>'string',
    'available_flag'=>'string',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function castAttribute($key, $value)
    {
        if (! is_null($value)) {
            return parent::castAttribute($key, $value);
        }
        switch ($this->getCastType($key)) {
            case 'int':
            case 'integer':
            return (int) 0;
            case 'real':
            case 'float':
            case 'double':
            return (float) 0;
            case 'enum':
            return '';
            case 'string':
            return '';
            case 'bool':
            case 'boolean':
            return false;
            case 'object':
            case 'array':
            case 'json':
            return [];
            case 'collection':
            return new BaseCollection();
            case 'date':
            return $this->asDate('0000-00-00');
            case 'datetime':
            return $this->asDateTime('0000-00-00');
            case 'timestamp':
            return '';
            default:
            return $value;
        }
    }
    public function card_details()
    {
        return $this->hasMany(\App\Models\CardDetails::class,'user_id','id');
    }

    public function scopeWithPackageAmount(Builder $query)
    {
        return $query->leftJoinSub(
            'select user_id, sum(packs) as total_diamonds from paymenthistory group by user_id',
            'paymenthistory_table',
            'paymenthistory_table.user_id',
            'users.id');
    }

     public function scopeWithVideoData(Builder $query)
    {
        return $query->leftJoinSub(
            'select user_id, (video) as videoslist from videos group by u_id',
            'videos_table',
            'videos_table.user_id',
            'users.id')->orderby('id','desc');
    }

     public function scopeWithFollowList(Builder $query)
    {
        return $query->leftJoinSub(
            'select user_id, (status)as follow_flge from follow_users group by u_id',
            'follow_users_table',
            'follow_users_table.user_id',
            'users.id');
    }

    public function follow_unfollow_flag(){
        return $this->hasMany(FollowUser::class,'user_id','id');
    }

    public function live_streaming_flag(){
        return $this->hasMany(LiveNotification::class,'follow_user','id');
    }

    // Streamers    

    public function scopeWithStreamerCountYear(Builder $query){
        $timestemp = \Carbon\Carbon::today();
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;
        return $query->leftJoinSub(
            'select user_id, sum(live_no) as live_no from live_notification group by user_id',
            'live_notification_table',
            'live_notification_table.user_id',
            'users.id')->where('created_at','>=',$date)->orderby('live_no','desc');
    }

    public function scopeWithStreamerCountData(Builder $query){
        $timestemp = \Carbon\Carbon::today();
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $timestemp)->year;
        return $query->leftJoinSub(
            'select user_id, sum(live_no) as live_no from live_notification group by user_id',
            'live_notification_table',
            'live_notification_table.user_id',
            'users.id')->where('created_at','>=',$date)->orderby('live_no','desc');
    }

    public function gift_diamonds_count(){
        return $this->hasOne('App\Models\GiftDiamond', 'id', 'receive_id')->sum('receive_id');
    }
}