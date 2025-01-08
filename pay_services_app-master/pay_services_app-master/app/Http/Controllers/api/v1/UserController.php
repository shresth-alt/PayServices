<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Str;
use App\UserAddress;
use App\VendorDetail;
use App\CustomerDetail;
use App\VendorService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\VendorPayment;

class UserController extends Controller {
  
  /** Creating new user */
  public function create(Request $request) {
    $validation = Validator::make($request->all(), array(
      'name' => 'required',
      'mobile' => 'required|numeric|unique:users|digits:10',
      'type' => 'required',
      'password' => 'sometimes|required'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else { # Validations passed create new user
      $user = User::create($request->only('name', 'mobile', 'type'));
      if ($user != null) {
        $user->api_token = Str::random(80);
        if ($request->type == 'vendor') {
          $user->password = Hash::make($request->password);
        }
        $user->update();
      }
      return response($user);
    }
  }

  public function add_user_address(Request $request) {
    $validation = Validator::make($request->all(), array(
      'address' => 'required',
      'city' => 'required',
      'postal_code' => 'required|numeric'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      $address = UserAddress::create(array(
        'user_id' => $request->user()->id,
        'address' => $request->address,
        'landmark' => $request->landmark,
        'city' => $request->city,
        'postal_code' => $request->postal_code
      ));
      return response()->json($address);
    }
  }

  public function user_addresses(Request $request) {
    $user = $request->user();
    $user_addresses = UserAddress::where('user_id', $user->id)->get();
    return response()->json($user_addresses);
  }

  public function updateUser(Request $request) {
    $user = $request->user();
    if ($user->type == 'vendor') {
      if ($request->has('email')) {
        $user->email = $request->email;
        $user->update();
      }
      $params = $request->only('father_name', 'dob', 'alt_mobile', 'cur_address', 'per_address', 'village', 'town', 'city', 'state', 'district', 'pincode', 'qualification');
      if ($user->detail == null) {
        $params['vendor_id'] = $user->id;
        VendorDetail::create($params);
      } else {
        $user->detail->update($params);
      }
      /** Uploading images */
      /** Base 64 Decode */
      if ($request->filled('aadhar_front')) {
        $file = base64_decode($request->aadhar_front);
        $file_name = time().'_aadhar_front.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->aadhar_front = $file_name;
        $vendor_detail->update();
      }
      if ($request->filled('aadhar_back')) {
        $file = base64_decode($request->aadhar_back);
        $file_name = time().'_aadhar_back.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->aadhar_back = $file_name;
        $vendor_detail->update();
      }
      if ($request->filled('driving_license')) {
        $file = base64_decode($request->driving_license);
        $file_name = time().'_driving_license.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->driving_license = $file_name;
        $vendor_detail->update();
      }
      if ($request->filled('pan_card')) {
        $file = base64_decode($request->pan_card);
        $file_name = time().'_pan_card.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->pan_card = $file_name;
        $vendor_detail->update();
      }
      if ($request->filled('photo')) {
        $file = base64_decode($request->photo);
        $file_name = time().'_photo.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->photo = $file_name;
        $vendor_detail->update();
      }
      if ($request->filled('cheque')) {
        $file = base64_decode($request->cheque);
        $file_name = time().'_cheque.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->cheque = $file_name;
        $vendor_detail->update();
      }
      if ($request->filled('signature')) {
        $file = base64_decode($request->signature);
        $file_name = time().'_signature.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->signature = $file_name;
        $vendor_detail->update();
        Log::info('under signature');
      }
      if ($request->filled('insurance')) {
        $file = base64_decode($request->insurance);
        $file_name = time().'_insurance.png';
        $destination_path = public_path('uploads');
        file_put_contents($destination_path . "/" . $file_name, $file);
        $vendor_detail = VendorDetail::where('vendor_id', $user->id)->first();
        $vendor_detail->insurance = $file_name;
        $vendor_detail->update();
      }
      /** Checking for selected services */
      if ($request->filled('services')) {
        $services = explode(",", $request->services);
        $user->services()->delete();
        foreach ($services as $key => $service) {
          VendorService::create(array(
            'vendor_id' => $user->id,
            'service_id' => $service
          ));
        }
      }
    } else if ($user->type == 'customer') {
      if ($request->has('email')) {
        $user->email = $request->email;
        $user->update();
      }
      $params = $request->only('alt_mobile', 'village', 'landmark', 'city', 'state', 'pincode');
      if ($user->detail == null) {
        $params['customer_id'] = $user->id;
        CustomerDetail::create($params);
      } else {
        $user->detail->update($params);
      }
    }
    return response()->json(array(
      'status' => 'ok',
      'message' => 'Your profile updated successfully!'
    ));
  }

  public function currentUser(Request $request) {
    $user = $request->user();
    $user->detail = $user->detail;
    if ($user->type == 'vendor') {
      $user->services = $user->services;
    }
    return response()->json($user);
  }

  public function updateVendorPayment(Request $request) {
    $user = $request->user();
    if ($request->type == 'membership_fee') {
      $user->membership_fee = 500;
      $user->update();
    } else if ($request->type == 'wallet_amount') {
      $user->wallet_balance += $request->amount;
      $user->update();
      VendorPayment::create(array(
        'vendor_id' => $user->id,
        'pay_type' => 'credit',
        'amount' => $request->amount
      ));
    }
    return response()->json(array(
      'status' => 'success',
      'message' => 'Amount updated',
      'wallet_balance' => $user->wallet_balance
    ));
  }

  public function resetPassword(Request $request) {
    $validation = Validator::make($request->all(), array(
      'password' => 'required',
      'confirm_password' => 'required|same:password',
      'mobile' => 'required|numeric|digits:10',
      'otp' => 'required|numeric|digits:4'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      $user = User::where('mobile', $request->mobile)->where('user_otp', $request->otp)->first();
      if ($user != null) {
        $user->password = Hash::make($request->password);
        $user->update();
        return response()->json(array(
          'status' => 'success',
          'message' => 'Password reset completed! Please login now'
        ));
      } else {
        $_errors[] = "Invalid user or otp";
        return response(['errors' => $_errors], 422);
      }
    }
  }
}
