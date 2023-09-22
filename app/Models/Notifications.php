<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'notifications';
    protected $fillable = [
        'user_id','follow_user','title','description','status','flag_status','follow_status','channel_id'
    ];
    protected $casts = [
        'user_id' => 'string',
        'follow_user' => 'string',
        'description' => 'string',
        'status' => 'string',
        'title' => 'string',
        'deleted_at' => 'timestamp',
        'flag_status' => 'string',
        'follow_status' => 'string',
        'channel_id' => 'string',
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

    public function receive_data()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
