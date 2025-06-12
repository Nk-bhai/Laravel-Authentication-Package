<?php

namespace Nk\SystemAuth\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdmin extends Model
{
    protected $table = 'superadmin';
    protected $fillable = ['email', 'password', 'key'];
}