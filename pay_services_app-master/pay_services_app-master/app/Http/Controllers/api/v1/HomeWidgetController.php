<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HomeWidget;
use App\Service;
use App\WidgetRequest;

class HomeWidgetController extends Controller {
  
  /** Sends home widgets and services with service images present */
  public function index(Request $request) {
    $widgets = HomeWidget::with(['image:id,public_id,format'])->select('id', 'title', 'icon')->get();
    $services = Service::with(['slider_image'])->where('slider_img', '!=', 0)->where('parent_id', 0)->get();
    return response()->json(array(
      'data' => array('widgets' => $widgets, 'services' => $services)
    ));
  }
  
  public function widget_request(Request $request) {
    $validation = Validator::make($request->all(), array(
      'comments' => 'required',
      'widget_id' => 'required'
    ));
    if ($validation->fails()) {
      $errors = $validation->errors();
      $_errors = [];
      foreach ($errors->all() as $message) $_errors[] = $message;
      return response(['errors' => $_errors], 422);
    } else {
      $widget = WidgetRequest::create(array(
        'user_id' => $request->user()->id,
        'widget_id' => $request->widget_id,
        'comments' => $request->comments
      ));
      return response()->json($widget);
    }
  }
}
