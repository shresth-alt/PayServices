<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeWidget extends Model
{
  public function image() {
    return $this->hasOne('App\Image', 'id', 'icon');
  }
}
