<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller {
  /**
  * Authenticating user
  */
  public function authenticate(Request $request) {
    $validation = Validator::make($request->all(), array(
      'mobile' => 'required|numeric|digits:10',
      'otp' => 'required|numeric|digits:4'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else { # Validations passed create new user
      $user = User::where('mobile', $request->mobile)
      ->where('user_otp', $request->otp)
      ->where('type', 'customer')
      ->first();
      if ($user != null) {
        $user->api_token = Str::random(80);
        $user->update();
        return response()->json($user);
      } else {
        return response(['errors' => ['Invalid User']], 404);
      }
    }
  }

  public function authenticateWithPassword(Request $request) {
    $validation = Validator::make($request->all(), array(
      'mobile' => 'required|numeric|digits:10',
      'password' => 'required'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else { # Validations passed create new user
      $user = User::where('mobile', $request->mobile)
      ->where('type', 'vendor')
      ->first();
      if ($user != null) {
        if (Hash::check($request->password, $user->password)) {
          $user->api_token = Str::random(80);
          $user->update();
          return response()->json($user);
        } else {
          return response(['errors' => ['Invalid Password']], 404);
        }
      } else {
        return response(['errors' => ['Invalid User']], 404);
      }
    }
  }
  
}
