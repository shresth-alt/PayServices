<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VendorPayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorPaymentController extends Controller {
  /** Returns vendor payments */
  public function getPaymentsHistory(Request $request) {
    $user = $request->user();
    $payments = DB::table('vendor_payments')
      ->leftJoin('orders', 'vendor_payments.order_id', '=', 'orders.id')
      ->select('orders.order_amount', 'orders.service_charge', 'orders.commission', 'orders.commission_gst', 'vendor_payments.*')
      ->where('vendor_payments.vendor_id', $user->id)
      ->orderByDesc('vendor_payments.created_at')
      ->get();
    foreach ($payments as $payment) {
      if ($payment->pay_type == 'credit') {
        $payment->order_amount = 0;
        $payment->service_charge = 0;
        $payment->commission = 0;
        $payment->commission_gst = 0;
      }
      $payment->pay_type = ucfirst($payment->pay_type);
      $payment->created_at = Carbon::createFromDate($payment->created_at)->format('F d, Y');
    }
    return response()->json($payments);
  }
}
