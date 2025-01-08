<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\UserAddress;

class UserAddressController extends Controller {
  public function index(Request $request) {

  }

  public function delete(Request $request, $id) {
    $user = $request->user();
    $address = $user->addresses->find($id);
    if ($address != null) {
      $address->delete();
      return response()->json(array(
        'status' => 'success',
        'message' => 'Address has been deleted'
      ));
    } else {
      $errors[] = "Invalid address";
      return response()->json(array(
        'status' => 'error',
        'errors' => $errors
      ));
    }
  }
}
