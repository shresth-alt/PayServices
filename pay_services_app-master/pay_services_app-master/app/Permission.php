<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = array(
        'user_id', 'permission'
      );
      protected $hidden = array(
        'created_at', 'updated_at'
      );
}
