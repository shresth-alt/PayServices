<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeviceController extends Controller {
  
  /** Update token in users table */
  public function updateUserDeviceToken(Request $request) {  
    $user = $request->user();
    $user->device_token = $request->token;
    $user->update();
    return response()->json(['status' => 'ok']);
  }
}
