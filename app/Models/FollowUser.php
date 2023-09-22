<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class FollowUser extends Model
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table = 'follow_users';
    protected $fillable = [
        'user_id', 'followed_user_id','status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'string',
        'followed_user_id' => 'string',
        'updated_at' => 'date:Y-m-d H:i:s',
        'created_at' => 'date:Y-m-d H:i:s',
        'deleted_at' => 'date:Y-m-d H:i:s',
    ];

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

    public function followStatus()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function UserFollowerData()
    {
        return $this->hasOne('App\Models\User', 'id', 'followed_user_id');
    }

    public function UserFollowingData()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function UserFollowerDataAll()
    {
        return $this->hasOne('App\Models\User', 'id', 'followed_user_id')->withPackageAmount();
    }

    public function UserFollowingAll()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->withPackageAmount();
    }

    public function scopeWithPackageAmount(Builder $query)
    {
        return $query->leftJoinSub(
            'select user_id, sum(packs) as total_diamonds from paymenthistory group by user_id',
            'paymenthistory_table',
            'paymenthistory_table.user_id',
            'users.id');
    }

}


