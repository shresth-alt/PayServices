<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Log;

class VendorDetail extends Model {

  protected $fillable = array(
    'vendor_id', 'father_name', 'dob', 'alt_mobile', 'cur_address', 'per_address', 'village', 
    'town', 'city', 'state', 'district', 'pincode', 'qualification'
  );
  
}
