<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
  
  protected $fillable = [
    'name', 'mobile', 'type', 'wallet_balance', 'email', 'password',
  ];

  
  
  /**
  * The attributes that should be hidden for arrays.
  *
  * @var array
  */
  protected $hidden = [
    'password', 'remember_token', 'created_at', 'updated_at'
  ];
  
  /**
  * The attributes that should be cast to native types.
  *
  * @var array
  */
  protected $casts = [
    'email_verified_at' => 'datetime',
  ];

  public function addresses() {
    return $this->hasMany('App\UserAddress');
  }

  public function detail() {
    if ($this->type == 'vendor') return $this->hasOne('App\VendorDetail', 'vendor_id', 'id');
    else return $this->hasOne('App\CustomerDetail', 'customer_id', 'id');
  }

  public function pincodes() {
    return $this->hasMany('App\VendorPincode', 'vendor_id', 'id');
  }

  public function services() {
    return $this->hasMany('App\VendorService', 'vendor_id', 'id');
  }

}
