<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WidgetRequest extends Model {
  protected $fillable = array(
    'user_id', 'widget_id', 'comments'
  );
}
