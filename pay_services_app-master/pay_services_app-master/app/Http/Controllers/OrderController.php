<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\User;
use App\Permission;
use Carbon\Carbon;
use Session;
use App\Events\OrderAccepted;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderController extends Controller {
  
  public function list(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_order')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $filters = array(
        'date_from' => '',
        'date_to' => '',
      );
      $query = DB::table('orders')
      ->select('orders.*', 'orders.id as order_id' , 'users.mobile as user_mobile' , 'users.name as customer_name', 'services.name as service_name', 'vendor_details.*' , 'vendor.name as vendor_name', 'vendor_details.alt_mobile as vendor_mobile' , 'vendor_details.village as vendor_village', 'vendor_details.city as vendor_city', 'vendor_details.state as vendor_state', 'vendor_details.pincode as vendor_pincode', 'orders.address as billing_address','customer_details.alt_mobile as customer_mobile', )
      ->join('users', 'users.id', '=', 'orders.user_id')
      ->leftJoin('users as vendor', 'vendor.id', '=', 'orders.vendor_id')
      ->join('services', 'services.id', '=', 'orders.service_id')
      ->leftJoin('customer_details', 'customer_details.customer_id', '=', 'orders.user_id')
      ->leftJoin('vendor_details', 'vendor_details.vendor_id', '=', 'orders.vendor_id')
      ->orderByDesc('orders.id');
      if ($request->filled('squery') && $request->squery[0] == '#') {
        $search_by_id = str_replace('#','',$request->squery);
        $query = $query->where('orders.id', 'like', '%'.$search_by_id.'%');
      } elseif ($request->filled('squery')) {
        $query = $query->where('users.mobile', 'like', '%'.$request->squery.'%')->orWhere('users.name', 'like', '%'.$request->squery.'%');
      }
      if ($request->filled('date_from') && $request->filled('date_to')) {
        $query = $query->whereDate('orders.service_date', ">=", Carbon::createFromFormat('d/m/Y', $request->date_from)->format('Y-m-d'));
        $query = $query->whereDate('orders.service_date', "<=", Carbon::createFromFormat('d/m/Y', $request->date_to)->format('Y-m-d'));
        $filters['date_from'] = $request->date_from;
        $filters['date_to'] = $request->date_to;
      }
      if ($request->filled('status')) {
        $query = $query->where('orders.status', 'like', '%'.$request->status.'%');
      }
      $orders=$query->paginate(env('ITEMS_PER_PAGE'));
      $count = $query->count();
      if ($request->excel_export == 0) {
        return view('admin.orders.list', compact('orders', 'count', 'filters' ));
      } else {
        try {
          $fileLocation = public_path('export/Orders.xlsx');
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $styleArray = [ 'font' => [ 'bold' => true ] ];
          $sheet->getStyle('A1:AA1')->applyFromArray($styleArray);
          $alphas = range('A', 'Z');
          $alphas[] = 'AA';
          // echo 'hell**';
          $headers = array(
            'Order Id' , 'Customer Name' , 'Vendor Name' , 'Service' , 'Service Date' , 'Status' , 'Pending Status' , 'Order Status Date' , 'Payment Status' , 'Order Amount' , 'Customer Mobile' , 'Customer Billing Mobile' , 'Customer Billing Address' , 'Customer Billing Village' , 'Customer Billing Landmark' , 'Customer Billing City' , 'Customer Billing State' , 'Customer Billing Pincode' , 'Vendor Mobile' , 'Vendor Current Address' , 'Vendor Permanent Address', 'Vendor Village' , 'Vendor Town' , 'Vendor City' , 'Vendor State' , 'Vendor District' , 'Vendor Pincode',
          );
          foreach ($headers as $key => $header) {
            $sheet->setCellValue($alphas[$key] . '1', $header);
          }
          $rows = 2;
          foreach ($orders as $order) {
            $sheet->setCellValue('A' . $rows, $order->order_id);
            $sheet->setCellValue('B' . $rows, $order->customer_name);
            $sheet->setCellValue('C' . $rows, $order->vendor_name);
            $sheet->setCellValue('D' . $rows, $order->service_name);
            if (!empty($order->service_date)) {
              $service_date = Carbon::createFromFormat('Y-m-d H:i:s', $order->service_date)->format('d/m/Y g:i A');
            } else {
              $service_date = "";
            }
            $sheet->setCellValue('E' . $rows, $service_date);
            $sheet->setCellValue('F' . $rows, $order->status);
            $sheet->setCellValue('G' . $rows, $order->comments);
            if (!empty($order->order_status_date)) {
              $status_date = Carbon::createFromFormat('Y-m-d H:i:s', $order->order_status_date)->format('d/m/Y g:i A');
            } else {
              $status_date = "";
            }
            $sheet->setCellValue('H' . $rows, $status_date);
            if ($order->payment_status != 'na') {
              $payment_status = $order->payment_status;
            } else {
              $payment_status = "";
            }
            $sheet->setCellValue('I' . $rows, $payment_status);
            $sheet->setCellValue('J' . $rows, $order->order_amount);
            $sheet->setCellValue('K' . $rows, $order->user_mobile);
            $sheet->setCellValue('L' . $rows, $order->billing_alt_mobile);
            $sheet->setCellValue('M' . $rows, $order->billing_address);
            $sheet->setCellValue('N' . $rows, $order->billing_village);
            $sheet->setCellValue('O' . $rows, $order->billing_landmark);
            $sheet->setCellValue('P' . $rows, $order->billing_city);
            $sheet->setCellValue('Q' . $rows, $order->billing_state);
            $sheet->setCellValue('R' . $rows, $order->billing_pincode);
            $sheet->setCellValue('S' . $rows, $order->vendor_mobile);
            $sheet->setCellValue('T' . $rows, $order->cur_address);
            $sheet->setCellValue('U' . $rows, $order->per_address);
            $sheet->setCellValue('V' . $rows, $order->vendor_village);
            $sheet->setCellValue('W' . $rows, $order->town);
            $sheet->setCellValue('X' . $rows, $order->vendor_city);
            $sheet->setCellValue('Y' . $rows, $order->vendor_state);
            $sheet->setCellValue('Z' . $rows, $order->district);
            $sheet->setCellValue('AA' . $rows, $order->vendor_pincode);
            
            $rows++;
          }
          $writer = new Xlsx($spreadsheet);
          $writer->save($fileLocation);
          return response()->download($fileLocation);
        } catch(Exception $e) {
        }
      }
    }
  }
  
  public function edit(Request $request, $id) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'edit_order')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $order = Order::find($id);
      $vendors = User::where('type', 'vendor')->get();
      return view('admin.orders.edit', compact('order', 'vendors'));
    }
  }
  
  /** Update the order */
  public function update(Request $request, $id) {
    $order = Order::find($id);
    if ($request->status != 'cancelled') {
      if ($request->filled('vendor_id')) {
        $order->vendor_id = $request->filled('vendor_id') ? $request->vendor_id : 0;
      }
    } else {
      $order->status = $request->status;
    }
    $order->update();
    if ($request->status != 'cancelled') {
      if ($request->filled('vendor_id')) {
        event(new OrderAccepted($order));
      }
    }
    $request->session()->flash('state', 'Order updated successfully');
    return redirect('admin/orders');
  }
  
  public function orderDetails(Request $request, $id) {
    $order = Order::find($id);
    $items = $order->items;       
    $count = $items->count();
    $customer = DB::table('users')->select('customer_details.*', 'users.name as customer_name', 'users.mobile as user_mobile', 'users.email as user_email')
    ->leftJoin('orders', 'orders.user_id' , 'users.id')
    ->leftJoin('customer_details', 'customer_details.customer_id', 'users.id')
    ->where('orders.id', $id)->first();
    return view('admin.orders.order_details', compact('items', 'count', 'order', 'id','customer'));
  }
  public function delete($id) {
    Order::find($id)->delete();
    Session::flash('state', 'Order deleted successfully');
    return redirect(route('orders.list'));
  }
  
  public function accounts(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_accounts')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $filters = array(
        'date_from' => '',
        'date_to' => '',
      );
      $query = DB::table('orders')
      ->select('orders.*', 'ratings_reviews.*' , 'orders.id as order_id' , 'users.mobile as user_mobile' , 'users.name as customer_name', 'services.name as service_name', 'vendor_details.*' , 'vendor.name as vendor_name', 'vendor_details.alt_mobile as vendor_mobile' , 'vendor_details.village as vendor_village', 'vendor_details.city as vendor_city', 'vendor_details.state as vendor_state', 'vendor_details.pincode as vendor_pincode', 'orders.address as billing_address','customer_details.alt_mobile as customer_mobile', )
      ->leftJoin('users', 'users.id', '=', 'orders.user_id')
      ->leftJoin('users as vendor', 'vendor.id', '=', 'orders.vendor_id')
      ->leftJoin('services', 'services.id', '=', 'orders.service_id')
      ->leftJoin('customer_details', 'customer_details.customer_id', '=', 'orders.user_id')
      ->leftJoin('vendor_details', 'vendor_details.vendor_id', '=', 'orders.vendor_id')
      ->leftJoin('ratings_reviews', 'ratings_reviews.order_id', '=', 'orders.id')
      ->orderByDesc('orders.id');
      
      if ($request->filled('squery') && $request->squery[0] == '#') {
        $search_by_id = str_replace('#','',$request->squery);
        $query = $query->where('orders.id', 'like', '%'.$search_by_id.'%');
      } elseif ($request->filled('squery')) {
        $query = $query->where('users.mobile', 'like', '%'.$request->squery.'%')->orWhere('users.name', 'like', '%'.$request->squery.'%');
      }
      if ($request->filled('date_from') && $request->filled('date_to')) {
        $query = $query->whereDate('orders.created_at', ">=", Carbon::createFromFormat('d/m/Y', $request->date_from)->format('Y-m-d'));
        $query = $query->whereDate('orders.created_at', "<=", Carbon::createFromFormat('d/m/Y', $request->date_to)->format('Y-m-d'));
        $filters['date_from'] = $request->date_from;
        $filters['date_to'] = $request->date_to;
      }
      $details = $query->where('status', 'completed')->where('payment_status', 'completed')->paginate(env('ITEMS_PER_PAGE'));
      
      /** Calculating profit and commission */
      $query = DB::table('orders')->select('orders.*')->where('status', 'completed')->where('payment_status', 'completed');
      if ($request->filled('squery')) $query = $query->where('orders.id', 'like', '%'.$request->squery.'%');
      if ($request->filled('date_from') && $request->filled('date_to')) {
        $query = $query->whereDate('orders.created_at', ">=", Carbon::createFromFormat('d/m/Y', $request->date_from)->format('Y-m-d'));
        $query = $query->whereDate('orders.created_at', "<=", Carbon::createFromFormat('d/m/Y', $request->date_to)->format('Y-m-d'));
        $filters['date_from'] = $request->date_from;
        $filters['date_to'] = $request->date_to;
      }
      $count = $details->count();
      $orders = $query->get();
      $commission = $orders->sum('commission');
      $commission_gst = $orders->sum('commission_gst');
      $total_profit = $commission - $commission_gst;
      if ($request->filled('squery')) {
        $filters['search'] = $request->squery;
      }
      if ($request->excel_export == 0) {
        return view('admin.orders.accounts', compact('details', 'commission_gst' , 'filters', 'total_profit', 'count'));
      } else {
        try {
          $fileLocation = public_path('export/Accounts.xlsx');
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $styleArray = [ 'font' => [ 'bold' => true ] ];
          $sheet->getStyle('A1:AA1')->applyFromArray($styleArray);
          $alphas = range('A', 'Z');
          $alphas[] = 'AA';
          $headers = array(
            'Order Id', 'Customer Name' , 'Customer Mobile' , 'Customer Billing Mobile',  'Total Amount', 'Commission', 'Commission GST', 'Profit', 'Order Date' , 'Completion Date' , 'Customer Billing Address' , 'Customer Billing Village', 'Customer Billing Landmark' , 'Customer Billing City' , 'Customer Billing State', 'Customer Billing Pincode' , 'Vendor Name', 'Vendor Ratings' , 'Vendor Mobile', 'Vendor Current Address', 'Vendor Permanent Address', 'Vendor Village', 'Vendor Town', 'Vendor City', 'Vendor State', 'Vendor District', 'Vendor Pincode' ,
          );
          foreach ($headers as $key => $header) {
            $sheet->setCellValue($alphas[$key] . '1', $header);
          }
          $rows = 2;
          foreach ($details as $detail) {
            if ($detail->status == 'completed' && $detail->payment_status == 'completed') {
              $sheet->setCellValue('A' . $rows, $detail->order_id);
              $sheet->setCellValue('B' . $rows, $detail->customer_name);
              $sheet->setCellValue('C' . $rows, $detail->user_mobile);
              if (!empty($detail->billing_alt_mobile)) {
                $mobile = $detail->billing_alt_mobile;
              } else {
                $mobile = '';
              }
              $sheet->setCellValue('D' . $rows, $mobile);
              
              $sheet->setCellValue('E' . $rows, $detail->order_amount);
              $sheet->setCellValue('F' . $rows, $detail->commission);
              $sheet->setCellValue('G' . $rows, $detail->commission_gst);
              $sheet->setCellValue('H' . $rows, $detail->commission - $detail->commission_gst);
              $sheet->setCellValue('I' . $rows, Carbon::createFromFormat('Y-m-d H:i:s', $detail->created_at)->format('d/m/Y g:i A'));
              if ($detail->order_status_date != NULL) {
                $order_status_date = Carbon::createFromFormat('Y-m-d H:i:s', $detail->order_status_date)->format('d/m/Y g:i A');
              } else {
                $order_status_date = 'NA';
              } 
              $sheet->setCellValue('J' . $rows, $order_status_date);
              
              $sheet->setCellValue('K' . $rows, $detail->address);
              $sheet->setCellValue('L' . $rows, $detail->billing_village);
              $sheet->setCellValue('M' . $rows, $detail->billing_landmark);
              $sheet->setCellValue('N' . $rows, $detail->billing_city);
              $sheet->setCellValue('O' . $rows, $detail->billing_state);
              $sheet->setCellValue('P' . $rows, $detail->billing_pincode);
              $sheet->setCellValue('Q' . $rows, $detail->vendor_name);
              $sheet->setCellValue('R' . $rows, $detail->rating);
              $sheet->setCellValue('S' . $rows, $detail->vendor_mobile);
              $sheet->setCellValue('T' . $rows, $detail->cur_address);
              $sheet->setCellValue('U' . $rows, $detail->per_address);
              $sheet->setCellValue('V' . $rows, $detail->vendor_village);
              $sheet->setCellValue('W' . $rows, $detail->town);
              $sheet->setCellValue('X' . $rows, $detail->vendor_city);
              $sheet->setCellValue('Y' . $rows, $detail->vendor_state);
              $sheet->setCellValue('Z' . $rows, $detail->district);
              $sheet->setCellValue('AA' . $rows, $detail->vendor_pincode);
              
              
              
              $rows++;
            }
          }
          $writer = new Xlsx($spreadsheet);
          $writer->save($fileLocation);
          return response()->download($fileLocation);
        } catch(Exception $e) {
        }
      } 
    }
  }
}