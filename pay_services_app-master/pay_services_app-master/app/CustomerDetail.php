<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model {
  protected $fillable = array(
    'customer_id', 'alt_mobile', 'village', 'landmark',
    'city', 'pincode', 'state'
  );
}
