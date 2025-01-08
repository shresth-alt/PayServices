<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller {
  /** Returning services to app */
  public function index(Request $request) {
    $query = Service::with(['image', 'slider_image'])->where('is_active', true);
    if ($request->filled('parent_id')) {
      $parent_id = $request->parent_id;
      $query->where('parent_id', $parent_id);
    } else {
      $query->where('parent_id', 0);
    }
    $services = $query->get();
    return response()->json(array(
      'data' => $services
    ));
  }

  /** Returning services to vendor app for selecting services */
  public function servicesForVendor(Request $request) {
    $query = "SELECT * FROM services WHERE parent_id IN (SELECT id FROM services WHERE parent_id = 0)";
    $services_array = array();
    $services = DB::select(DB::raw($query)); /** Fetching level 2 services */
    foreach ($services as $key => $service) {
      $child_services = Service::where('parent_id', $service->id)->get();
      foreach ($child_services as $key => $child_service) {
        $services_array[] = array(
          'id' => $child_service->id, 
          'service_name' => $service->name. " " . $child_service->name
        );
      }
    }
    return response()->json($services_array);
  }
}
