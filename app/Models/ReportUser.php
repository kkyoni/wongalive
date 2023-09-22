<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ReportUser extends Model

{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $table = 'report_users';
    protected $fillable = [
        'category_id', 'user_id', 'receive_id','description','status'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'category_id' => 'string',
        'user_id' => 'string',
        'receive_id' => 'string',
        'description' => 'string',
        'updated_at' => 'date:Y-m-d H:i:s',
        'created_at' => 'date:Y-m-d H:i:s',
        'deleted_at' => 'date:Y-m-d H:i:s',
    ];

    public function reportCategory()
    {
        return $this->hasOne(ReportCategory::class, 'id', 'category_id');
    }

    public function userget()
    {
        return $this->hasOne(User::class, 'id','user_id');
    }
    public function receget()
    {
        return $this->hasOne(User::class, 'id','receive_id');
    }
}
