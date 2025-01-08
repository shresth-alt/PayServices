<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model {
  
  protected $fillable = array(
    'id', 'name', 'description', 'parent_id', 'icon','slider_img', 'is_active','title' 
  );

  protected $hidden = array(
    'created_at', 'updated_at'
  );

  public function image() {
    return $this->hasOne('App\Image', 'id', 'icon');
  }

  public function slider_image() {
    return $this->hasOne('App\Image', 'id', 'slider_img');
  }

  public function parentIDImage() {
    return $this->hasOne('App\Image', 'id', 'icon');
  }
}
