<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
  protected $fillable = array(
    'user_id',
    'address',
    'landmark',
    'city',
    'postal_code'
  );
  protected $hidden = array(
    'created_at',
    'updated_at'
  );
}
