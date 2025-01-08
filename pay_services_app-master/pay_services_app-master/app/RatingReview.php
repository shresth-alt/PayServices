<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RatingReview extends Model {
  protected $table = 'ratings_reviews';
  protected $fillable = array(
    'order_id', 'vendor_id', 'rating', 'review'
  );
}
