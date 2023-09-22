<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LiveNotification extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'live_notification';
    protected $fillable = ['user_id','follow_user','u_id','status','dual_status','chat_flag','control_buttons','viewer','filters'];
    protected $casts = [
        'user_id' => 'string',
        'follow_user' => 'string',
        'u_id' => 'string',
        'status' => 'string',
        'dual_status' => 'string',
        'chat_flag'  => 'string',
        'control_buttons'  => 'string',
        'filters' => 'string',
        'deleted_at' => 'timestamp',
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

    public function UserliveData()
    {
        return $this->hasOne('App\Models\User', 'id', 'follow_user');
    }
    
    public function hostliveData()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function user_detail()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


}