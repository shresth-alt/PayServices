<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
  protected $fillable = array(
    'order_id',
    'item_name',
    'price',
    'image'
  );
}
