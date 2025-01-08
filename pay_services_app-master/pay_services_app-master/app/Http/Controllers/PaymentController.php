<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;

class PaymentController extends Controller
{
  public function processOrderPayment(Request $request) {

  }

  public function initPayment(Request $request) {
    $payment = Payment::where('order_id', $request->order_id)->first();
    if ($payment != null) { 
      $payment->payment_method = $request->payment_method;
      $payment->update();
    } else {
      Payment::create(array(
        'order_id' => $request->order_id,
        'amount' => 100,
        'payment_method' => $request->payment_method,
        'payment_meta' => json_encode(array('amount' => 20))
      ));
      return response()->json(array(
        'status' => 'success',
        'message' => 'Payment has been initialized'
      ));
    }
  }
}
