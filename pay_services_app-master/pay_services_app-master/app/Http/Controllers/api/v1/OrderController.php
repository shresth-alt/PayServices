<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Order;
use App\User;
use App\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\OrderCreated;
use App\Events\OrderAccepted;
use App\Events\OrderCompleted;
use App\OrderItem;
use App\Payment;
use App\RatingReview;
use App\VendorPayment;

class OrderController extends Controller {
  
  public function index(Request $request) {
    $user = $request->user();
    if ($user->type == 'customer') {
      $orders = DB::table('orders')
      ->join('services', 'orders.service_id', '=', 'services.id')
      ->select('orders.*', 'services.name as service_name')
      ->where('orders.user_id', '=', $user->id)
      ->get();
      foreach ($orders as $order) {
        $order->service_date = Carbon::createFromDate($order->service_date)->format('F d, Y');
      }
    } else {
      $vendor_services = $user->services;
      $vendor_pincodes = $user->pincodes;
      $vendor_services_ids = $_vendor_pincodes = array();
      foreach ($vendor_services as $vendor_service) {
        $vendor_services_ids[] = $vendor_service->service_id;
      }
      foreach ($vendor_pincodes as $vendor_pincode) {
        $_vendor_pincodes[] = $vendor_pincode->pincode;
      }
      $orders = DB::table('orders')
      ->join('services', 'orders.service_id', '=', 'services.id')
      ->join('users', 'orders.user_id', '=', 'users.id')
      ->select('orders.*', 'services.name as service_name', 'users.name as customer_name', 'users.mobile as customer_mobile')
      ->whereIn('orders.service_id', $vendor_services_ids)
      ->whereIn('orders.postal_code', $_vendor_pincodes)
      ->where('orders.vendor_id', NULL)
      ->orWhere('orders.vendor_id', $user->id)
      ->where('orders.is_rated', false)
      ->get();
      foreach ($orders as $order) {
        $order->service_date = Carbon::createFromDate($order->service_date)->format('F d, Y');
        if ($order->status == 'completed') {
          $payment = Payment::where('order_id', $order->id)->first();
          if ($payment == null) {
            $order->payment_method = ''; 
          } else {
            $order->payment_method = $payment->payment_method;
          }
        } else {
          $order->payment_method = '';
        }
      }
    }
    return response()->json($orders);
  }

  public function getOrderDetail(Request $request, $id) {
    $order = DB::table('orders')
      ->join('services', 'orders.service_id', '=', 'services.id')
      ->join('users', 'orders.user_id', '=', "users.id")
      ->leftJoin('customer_details', 'orders.user_id', '=', "customer_details.customer_id")
      ->select('orders.*', 'services.name as service_name', 'services.charges', 'users.name', 'users.email', 'users.mobile', 
      'customer_details.alt_mobile', 'customer_details.village as customer_village', 'customer_details.landmark as customer_landmark', 
      'customer_details.city as customer_city', 'customer_details.pincode as customer_pincode', 'customer_details.state as customer_state')
      ->where('orders.id', $id)
      ->first();
    return response()->json($order);
  }

  public function create(Request $request) {
    $validation = Validator::make($request->all(), array(
      'service_id' => 'required|numeric',
      'address' => 'required',
      'city' => 'required',
      'postal_code' => 'required|numeric',
      'service_date' => 'required|date'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      /** Checking if we have this service in this pincode */
      $vendors = DB::table('vendor_services')
        ->join('users', 'vendor_services.vendor_id', '=', 'users.id')
        ->join('vendor_pincodes', 'vendor_pincodes.vendor_id', '=', 'users.id')
        ->where('vendor_services.service_id', $request->service_id)
        ->where('vendor_pincodes.pincode', $request->postal_code)
        ->select('users.*')
        ->count();
      if ($vendors == 0) {
        $_errors[] = "This services is not available in this area as of now.";
        return response()->json(array('errors' => $_errors), 422);
      } else {
        $params = $request->only('service_id', 'service_date', 'address', 'city', 'postal_code');
        $params['service_date'] = $params['service_date'].' '.date('H:i:s');
        $params['user_id'] = $request->user()->id;
        $order = Order::create($params);
        /** Send notification to all vendors who serve this service */
        event(new OrderCreated($order, $request->service_id, $request->postal_code));
        return response()->json($order);
      }
    }
  }

  public function cancelOrder(Request $request, $order_id) {
    $order = Order::find($order_id);
    if ($order->created_at > Carbon::now()->format('d/m/Y')) {
      $order->status = $request->order_status;
      $order->cancellation_reason = $request->cancellation_reason;
      // $order->order_status_date = date('Y-m-d');
      $order->update();
      return response()->json(array(
        'status' => 'success',
        'message' => "Your order has been cancelled."
      ));
    } else {
      return response()->json(array(
        'status' => 'error',
        'errors' => array("Sorry! The order can be cancelled on the same day!")
      ), 422);
    }
  }

  public function acceptOrder(Request $request, $order_id) {
    $user = $request->user();
    $order = Order::find($order_id);
    if ($order->status == 'pending') {
      $order->status = 'accepted';
      $order->vendor_id = $user->id;
      // $order->order_status_date = date('Y-m-d');
      $order->update();
      /** Send customer a notification to inform him about order acceptance */
      event(new OrderAccepted($order));
      return response()->json(array(
        'status' => 'ok',
        'message' => "Booking has been accepted"
      ));
    } else if ($order->status == 'cancelled') {
      return response()->json(array(
        'status' => 'error',
        'errors' => array("Booking has been cancelled by customer")
      ), 422);
    } else {
      return response()->json(array(
        'status' => 'error',
        'errors' => array("Booking has already been accepted by other vendor")
      ), 422);
    }
  }

  public function completeOrder(Request $request, $order_id) {
    $params = $request->all();
    if ($request->filled('items')) {
      $params['items'] = json_decode($request->items, true);
    }
    $validation = Validator::make($params, array(
      'charge_type' => 'required|in:visiting,service',
      'main_image' => 'nullable|exclude_if:charge_type,visiting|required',
      'serial_model_image' => 'nullable|exclude_if:charge_type,visiting|required',
      'items' => 'sometimes|exclude_if:charge_type,visiting|array',
      'items.*.item_name' => 'sometimes|required',
      'items.*.price' => 'sometimes|required|numeric',
      'items.*.image' => 'sometimes|required'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      /** Processing order images */
      try {
        $commission = $gst = 0;
        /** Order */
        DB::beginTransaction();
        $order = Order::find($order_id);
        if ($request->charge_type == 'visiting') {
          $order->service_charge = $order->order_amount = 100;
          $commission = (100 * 15 / 100);
          $gst = ($commission * 18 / 100);
          $commission += $gst;
        } else if ($request->charge_type == 'service') {
          $order_amount = $order->service_charge = $order->service->charges;
          $commission = ($order_amount * 15 / 100);
          $gst = ($commission * 18 / 100);
          $commission += $gst;
          if ($request->filled('items')) {
            $order_items = json_decode($request->items);
            foreach ($order_items as $key => $order_item) {
              $_order_item = OrderItem::create(array(
                'order_id' => $order_id,
                'item_name' => $order_item->item_name,
                'price' => $order_item->price
              ));
              $order_amount += $order_item->price;
              if (trim($order_item->image) != "") {
                $file = base64_decode($order_item->image);
                $file_name = 'order_item_'.$_order_item->id.'.png';
                $destination_path = public_path('uploads');
                file_put_contents($destination_path . "/" . $file_name, $file);
                $_order_item->image = $file_name;
                $_order_item->update();
              }
            }
          }
          $order->order_amount = $order_amount;
        }
        if ($request->filled('main_image')) {
          $file = base64_decode($request->main_image);
          $file_name = 'order_main_'.$order->id.'.png';
          $destination_path = public_path('uploads');
          file_put_contents($destination_path . "/" . $file_name, $file);
          $order->image = $file_name;
        }
        if ($request->filled('serial_model_image')) {
          $file = base64_decode($request->serial_model_image);
          $file_name = 'order_serial_model_'.$order->id.'.png';
          $destination_path = public_path('uploads');
          file_put_contents($destination_path . "/" . $file_name, $file);
          $order->serial_model_image = $file_name;
        }
        /** Billing details TO-DO */
        $order->billing_email = $request->email;
        $order->billing_alt_mobile = $request->alt_mobile;
        $order->billing_village = $request->village;
        $order->billing_landmark = $request->landmark;
        $order->billing_city = $request->city;
        $order->billing_state = $request->state;
        $order->billing_pincode = $request->pin_code;
        $order->status = 'completed';
        /** Invoice no assignment */
        $invoice_no = DB::table('orders')->max('invoice_no');
        if ($invoice_no < 28) $invoice_no = 27;
        $order->invoice_no = $invoice_no + 1;
        
        $order->payment_status = 'pending';
        $order->commission = $commission;
        $order->commission_gst = $gst;
        $order->charge_type = $request->charge_type;
        $order->order_status_date = date('Y-m-d H:i:s');
        $order->update();
        $order->vendor->wallet_balance -= $commission;
        $order->vendor->update();
        /** Create log entries for vendor payment deduction */
        VendorPayment::create(array(
          'vendor_id' => $order->vendor_id,
          'order_id' => $order->id,
          'pay_type' => 'debit',
          'amount' => $commission
        ));
        DB::commit();
        event(new OrderCompleted($order));
        return response()->json(array(
          'status' => 'success',
          'message' => 'Order has been completed successfully!'
        ));
      } catch(Exception $e) {
        DB::rollBack();
        return response()->json(array(
          'status' => 'error',
          'message' => 'Something went wrong!'
        ), 500);
      }
    }
  }

  public function testNotification(Request $request) {
    $order = Order::find(1);
    event(new OrderCreated($order, 18, '313001'));
  }

  public function saveRatingReview(Request $request, $id) {
    $validation = Validator::make($request->all(), array(
      'rating' => 'required|numeric'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      $order = Order::find($id);
      $rating = RatingReview::where('order_id', $id)->first();
      if ($rating == null) {
        $rating = RatingReview::create(array(
          'order_id' => $id,
          'vendor_id' => $order->vendor_id,
          'rating' => $request->rating,
          'review' => $request->review
        ));
        if ($rating != null) {
          $order->is_rated = true;
          $order->update();
        }
      } else {
        $rating->rating = $request->rating;
        $rating->review = $request->review;
        $rating->update();
      }
      return response()->json(array(
        'status' => 'success',
        'message' => 'Your rating and review have been saved successfully'
      ));
    }
  }

  public function updateOrderComment(Request $request, $id) {
    $validation = Validator::make($request->all(), array(
      'comments' => 'required'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      $order = Order::find($id);
      $order->comments = $request->comments;
      $order->update();
      return response()->json(array(
        'status' => 'success',
        'message' => 'Order comment has been added successfully'
      ));
    }
  }
}
