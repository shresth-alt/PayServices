<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Payment;

class PaymentController extends Controller {

  public function initPayment(Request $request) {
    $payment = Payment::where('order_id', $request->order_id)->first();
    if ($payment != null) {
      $payment->payment_method = trim($request->payment_method);
      $payment->update();
    } else {
      $order = Order::find($request->order_id);
      Payment::create(array(
        'order_id' => $request->order_id,
        'amount' => $order->order_amount,
        'payment_method' => trim($request->payment_method),
        'payment_meta' => json_encode(array('amount' => $order->order_amount))
      ));
    }
    return response()->json(array(
      'status' => 'success',
      'message' => 'Thank you for using our services! Please pay the amount using cash method!'
    ));
  }

  public function processOnlinePayment(Request $request) {
    /** Signature matching */
    $secret = 'tK7eXd7M6e6U5Asom55drr05';
    $generated_signature = hash_hmac('sha256', $request->razorpay_order_id . "|" . $request->razorpay_payment_id, $secret);
    if ($generated_signature == $request->razorpay_signature) {
      /** Signature matched, enter details into payments table */
      $order = Order::find($request->order_id);
      $payment = Payment::where('order_id', $request->order_id)->first();
      if ($payment != null) {
        $payment->payment_method = 'online';
        $payment->amount = $order->order_amount;
        $payment->payment_meta = json_encode($request->all());
        $payment->update();
      } else {
        Payment::create(array(
          'order_id' => $request->order_id,
          'amount' => $order->order_amount,
          'payment_method' => 'online',
          'payment_meta' => json_encode($request->all())
        ));
      }
      $order->payment_status = 'completed';
      $order-update();
      return response()->json(array(
        'status' => 'success',
        'message' => 'Payment was successful'
      ));
    } else {
      $_errors[] = 'Payment failed';
      return response()->json(array(
        'status' => 'error',
        'errors' => $_errors
      ), 422);
    }
  }

  public function processCashPayment(Request $request) {
    $order = Order::find($request->order_id);
    $payment = Payment::where('order_id', $request->order_id)->first();
    if ($payment != null) {
      $payment->payment_method = 'cash';
      $payment->amount = $order->order_amount;
      $payment->payment_meta = json_encode(array('amount' => $order->order_amount));
      $payment->update();
    } else {
      Payment::create(array(
        'order_id' => $request->order_id,
        'amount' => $order->order_amount,
        'payment_method' => 'online',
        'payment_meta' => json_encode(array('amount' => $order->order_amount))
      ));
    }
    $order->payment_status = 'completed';
    $order->update();
    return response()->json(array(
      'status' => 'success',
      'message' => 'Payment was successful'
    ));
  }
}
