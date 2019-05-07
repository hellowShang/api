<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    //指定表名
    protected $table = 'userinfo';
    public $timestamps = false;
}
