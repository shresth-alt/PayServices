<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  protected $fillable = array(
    'order_id', 'amount', 'payment_method', 'payment_meta'
  );
}
