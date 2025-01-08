<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Config\Repository;
use App\Service;
use App\Permission;
use Session;
use App\Image;

class ServiceController extends Controller {

  /** Add service view */
  public function add(Request $request) {
    //$services = Service::where('type', 'vendor')->get();
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'add_service')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $services = Service::all();
      return view('admin.services.add', compact('services'));
    }
}

  /** Add service */
  public function addService(Request $request) {
    $service = new Service;
    $service->name = $request->name;
    $service->description = $request->description;
    $service->charges = $request->charges;
    $service->parent_id = $request->parent_id;
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $cloudinary_response = \Cloudinary\Uploader::upload($request->file('image')->getRealPath());
      $image = Image::create(array(
        'public_id' => $cloudinary_response['public_id'],
        'format' => $cloudinary_response['format'],
        'meta' => json_encode($cloudinary_response)
      ));
      $service->icon = $image->id;
    }
    if ($request->hasFile('slider_image') && $request->file('slider_image')->isValid()) {
      $cloudinary_response = \Cloudinary\Uploader::upload($request->file('slider_image')->getRealPath());
      $image = Image::create(array(
        'public_id' => $cloudinary_response['public_id'],
        'format' => $cloudinary_response['format'],
        'meta' => json_encode($cloudinary_response)
      ));
      $service->slider_img = $image->id;
    }
    $service->is_active = $request->is_active;
    $service->save();
    $request->session()->flash('state', 'New service added successfully');
    return redirect('admin/services');
  }

  /** View Services */
  public function show(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_service')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $query = DB::table('services')
      ->leftJoin('images', 'images.id', '=', 'services.icon')
      ->leftJoin('images as slider_image', 'slider_image.id', '=', 'services.slider_img')
      ->leftJoin('services as parent_services', 'services.parent_id', '=', 'parent_services.id')
      ->select('services.id', 'services.name', 'services.description', 'services.is_active', 'images.public_id', 'images.format',
        'parent_services.name as parent_service_name', 'slider_image.public_id as slider_public_id', 'slider_image.format as slider_image_format');
      if ($request->filled('squery')) $query = $query->where('services.name', 'like', '%'.$request->squery.'%');
      $count = $query->count();
      $data = $query->paginate(env("ITEMS_PER_PAGE"));
      return view('admin.services.list', ['data' => $data, 'count' => $count]);
    }
  }

  /** Edit Services */
  public function edit(Request $request, $id) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'edit_service')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $data = Service::find($id);
      $services = Service::where('id', '<>', $id)->get();
      return view('admin.services.edit', compact('data', 'services'));
    }
  }

  /** Update the Services */
  public function updateService(Request $request, $id) {
    $service = Service::find($id);
    $service->name = $request->name;
    $service->description = $request->description;
    $service->charges = $request->charges;
    $service->parent_id = $request->parent_id;
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $cloudinary_response = \Cloudinary\Uploader::upload($request->file('image')->getRealPath());
      $image = Image::create(array(
        'public_id' => $cloudinary_response['public_id'],
        'format' => $cloudinary_response['format'],
        'meta' => json_encode($cloudinary_response)
      ));
      $service->icon = $image->id;
    }
    if ($request->hasFile('slider_image') && $request->file('slider_image')->isValid()) {
      $cloudinary_response = \Cloudinary\Uploader::upload($request->file('slider_image')->getRealPath());
      $image = Image::create(array(
        'public_id' => $cloudinary_response['public_id'],
        'format' => $cloudinary_response['format'],
        'meta' => json_encode($cloudinary_response)
      ));
      $service->slider_img = $image->id;
    }
    $service->is_active = $request->has('is_active') ? true : false;
    $service->update();
    $request->session()->flash('state', 'Service update successfully');
    return redirect('admin/services');
  }

  /** Delete Services */
  public function delete($id) {
    Service::find($id)->delete();
    Session::flash('state', 'Service deleted successfully');
    return redirect('admin/services');
  }
}

