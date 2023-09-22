<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class PaymentHistory extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'paymenthistory';
    protected $fillable = ['user_id','diamonds_id','transaction_id','card_id','amount','payment_status','packs','user_purch_date'];

 	protected $casts = [
        'user_id' => 'string',
        'diamonds_id' => 'string',
        'transaction_id' => 'string',
        'card_id' => 'string',
        'amount' => 'string',
        'payment_status' => 'string',
        'packs'=> 'string',
        'user_purch_date'=> 'string',
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

    public function card_details()
    {
        return $this->hasMany(\App\Models\CardDetails::class,'id','card_id');
    }


    public function diamonds_deatil()
    {
        return $this->hasMany(\App\Models\Diamond::class,'id','diamonds_id');
    }

    public function getuserName()
    {
        return $this->hasOne(\App\Models\User::class,'id','user_id');
    }

}
