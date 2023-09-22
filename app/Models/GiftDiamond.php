<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class GiftDiamond extends Model
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'gift_diamonds';
    protected $fillable = [
    'sender_id','receive_id','gift_diamond','unique_id','gift_id'

    ];

    protected $casts = [
        'sender_id' => 'string',
        'receive_id' => 'string',
        'gift_diamond' => 'string',
        'gift_id' => 'string',
        'unique_id' => 'string',
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

    public function GiftData()
    {
        return $this->hasOne('App\Models\Gift', 'id', 'gift_id');
    }

    public function receive_detail()
    {
        return $this->hasOne('App\Models\User', 'id', 'receive_id');
    }
    
    public function send_detail()
    {
        return $this->hasOne('App\Models\User', 'id', 'sender_id');
    }
}
