<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model {
  protected $visible = array(
    'public_id',
    'format'
  );

  protected $fillable = array(
    'public_id','format','meta'
  );
}
