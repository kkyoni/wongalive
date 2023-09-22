<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class LiveComment extends Model
{
	use Notifiable;
	use SoftDeletes;

protected $table = 'livecomment';
protected $fillable = ['user_id','u_id','follow_user','comment','status'];
protected $casts = [
'user_id' => 'string',
'u_id' => 'string',
'follow_user' => 'string',
'comment' => 'string',
'status' => 'string',
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
}