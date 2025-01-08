<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VendorService extends Model {
  protected $fillable = array(
    'vendor_id', 'service_id'
  );
  protected $hidden = array(
    'created_at', 'updated_at'
  );
}
