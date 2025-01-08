<?php

namespace App\Http\Middleware;

use Closure;

class CloudinaryConfig
{
  /**
  * Handle an incoming request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @return mixed
  */
  public function handle($request, Closure $next)
  {
    \Cloudinary::config(array(
      "cloud_name" => env('CLOUDINARY_CLOUD_NAME'),
      "api_key" => env('CLOUDINARY_API_KEY'),
      "api_secret" => env('CLOUDINARY_API_SECRET'),
      "secure" => true
    ));
    return $next($request);
  }
}
