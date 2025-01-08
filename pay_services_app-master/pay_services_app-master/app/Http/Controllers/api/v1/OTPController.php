<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\OTPLog;
use App\User;

class OTPController extends Controller {

  /** Sending OTP to provided number */
  public function sendOTP(Request $request) {
    $otp_per_day_limit = 3;
    $validation = Validator::make($request->all(), array(
      'mobile' => 'required|numeric|digits:10',
      'type' => 'required'
    ), array(
      'mobile.required' => 'Please enter mobile number',
      'mobile.numeric' => 'Mobile number can be digits only',
      'mobile.digits' => 'Mobile number should be 10 digits',
      'type.required' => 'Please enter OTP type',
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    }
     else { # Validations passed, send user a OTP
      $counts = OTPLog::where('mobile', $request->mobile)->whereDate('created_at', date('Y-m-d'))->count();
      $otp  = rand(1000, 9999);
      $message = "$otp is your OTP to verify your mobile number on Pay Services";
      if ($request->type == 'login' || $request->type == 'forgot_password') {
        $user = User::where('mobile', $request->mobile)->first();
        /** Checking if user exists */
        if ($user != null) {
          $user->user_otp = $otp;
          $user->update();
          if ($counts < $otp_per_day_limit) {
            $this->sendTextMessage($request->mobile, $otp);
            OTPLog::create(['mobile' => $request->mobile]);
            return response()->json(array('otp' => $otp));
          } else {
            return response()->json(array('errors' => array('Limit for OTP exhausted for today. Please try tomorrow')), 422);
          }
        } else {
          return response()->json(array('errors' => array('This number is not registered with us. Kindly signup!')), 422);
        }
      } else {
        if ($counts < $otp_per_day_limit) {
          $this->sendTextMessage($request->mobile, $otp);
          OTPLog::create(['mobile' => $request->mobile]);
          return response()->json(array('otp' => $otp));
        } else {
          return response()->json(array('errors' => array('Limit for OTP exhausted for today. Please try tomorrow')), 422);
        }
      }
    }
  }

  private function sendTextMessage($mobile, $otp) {
    $client = new Client(['verify' => false]);
    $response = $client->request('GET', env('SMS_BASE_URL') . "/otp", array(
      'query' => array(
        'extra_param' => json_encode(array('OTP' => $otp)),
        'authkey' => env('SMS_AUTH_KEY'),
        'template_id' => '5fc74c2dac5c5f3e6c4bbadc',
        'mobile' => "91{$mobile}",
        'invisible' => 1
      )
    ));
    $response = json_decode((string) $response->getBody(), true);
    return $response;
  }

}
