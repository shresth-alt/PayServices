<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model  {
  protected $fillable = array(
    'user_id',
    'service_id',
    'service_date',
    'address',
    'city',
    'postal_code'
  );

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function vendor() {
    return $this->belongsTo('App\User', 'vendor_id', 'id');
  }

  public function service() {
    return $this->hasOne('App\Service', 'id', 'service_id');
  }
  
  public function items() {
    return $this->hasMany('App\OrderItem', 'order_id', 'id');
  }
}
