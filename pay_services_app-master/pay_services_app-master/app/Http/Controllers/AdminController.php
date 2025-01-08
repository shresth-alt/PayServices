<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Service;
use App\User;
use App\Order;
use App\Permission;
use App\WidgetRequest;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
  public function dashboard(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_dashboard')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $services_count = Service::where('is_active', true)->count();
      $vendors_count = User::where('type', 'vendor')->count();
      $customers_count = User::where('type', 'customer')->count();
      $orders_count = Order::count();
      $completed_orders = Order::where('status', 'completed')->count();
      $pending_orders = Order::where('status', 'pending')->count();
      $cancelled_orders = Order::where('status', 'cancelled')->count();
      $widgets_count = DB::table('home_widgets')->count();
      return view('admin.dashboard', compact('services_count', 'vendors_count', 'customers_count', 'orders_count', 'completed_orders', 'pending_orders', 'cancelled_orders', 'widgets_count'));
    }
  }

}
