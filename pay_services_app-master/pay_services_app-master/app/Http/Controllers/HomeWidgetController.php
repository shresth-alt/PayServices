<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\HomeWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Image;
use App\Permission;
use Session;
use App\WidgetRequest;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class HomeWidgetController extends Controller
{

  /** Add service view */
  public function add(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'add_home_widgets')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      return view('admin.home-widgets.add');
    }
  }

  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function list(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_home_widgets')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      
      $query = DB::table('home_widgets')
      ->leftJoin('images', 'images.id', '=', 'home_widgets.icon')
      ->select('home_widgets.*', 'images.public_id', 'images.format');
      if ($request->filled('squery')) $query = $query->where('title', 'like', '%'.$request->squery.'%');
      $count = $query->count();
      $home_widgets = $query->paginate(env('ITEMS_PER_PAGE'));
      
      return view('admin.home-widgets.list', compact('home_widgets', 'count'));
  }
}
  
  /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response
  */
  
  
  /**
  * Store a newly created resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function store(Request $request) {
    $widget = new HomeWidget;
    $widget->title = $request->title;
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $cloudinary_response = \Cloudinary\Uploader::upload($request->file('image')->getRealPath());
      $image = Image::create(array(
        'public_id' => $cloudinary_response['public_id'],
        'format' => $cloudinary_response['format'],
        'meta' => json_encode($cloudinary_response)
      ));
      $widget->icon = $image->id;
    }
    $widget->save();
    $request->session()->flash('state', 'New widget added successfully');
    return redirect('admin/widgets');
  }
  
  /**
  * Display the specified resource.
  *
  * @param  \App\HomeWidget  $homeWidget
  * @return \Illuminate\Http\Response
  */
  public function show(HomeWidget $homeWidget)
  {
    //
  }
  
  /**
  * Show the form for editing the specified resource.
  *
  * @param  \App\HomeWidget  $homeWidget
  * @return \Illuminate\Http\Response
  */
  public function edit(Request $request, $id) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'edit_home_widgets')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $data = HomeWidget::find($id);
      return view('admin.home-widgets.edit', compact('data'));
    }
  }
  
  /**
  * Update the specified resource in storage.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \App\HomeWidget  $homeWidget
  * @return \Illuminate\Http\Response
  */
  public function update(Request $request, $id)
  {
    $widget = HomeWidget::find($id);
    $widget->title = $request->title;
    if ($request->hasFile('image') && $request->file('image')->isValid()) {
      $cloudinary_response = \Cloudinary\Uploader::upload($request->file('image')->getRealPath());
      $image = Image::create(array(
        'public_id' => $cloudinary_response['public_id'],
        'format' => $cloudinary_response['format'],
        'meta' => json_encode($cloudinary_response)
      ));
      $widget->icon = $image->id;
    }
    $widget->update();
    $request->session()->flash('state', 'Widget update successfully');
    return redirect('admin/widgets');
  }
  
  /**
  * Remove the specified resource from storage.
  *
  * @param  \App\HomeWidget  $homeWidget
  * @return \Illuminate\Http\Response
  */
  public function delete(Request $request, $id) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'delete_home_widgets')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      HomeWidget::find($id)->delete();
      Session::flash('state', 'Widget deleted successfully');
      return redirect('admin/widgets');
    }
  }

  public function widget_requests(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_requests')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
        $query = DB::table('widget_requests')
        ->leftJoin('users', 'widget_requests.user_id', '=', 'users.id')
        ->leftJoin('home_widgets', 'widget_requests.widget_id', '=', 'home_widgets.id')
        ->leftJoin('customer_details', 'customer_details.customer_id', '=', 'users.id')
        ->select('widget_requests.*', 'widget_requests.id as widget_request_id' ,'users.mobile', 'users.name', 'home_widgets.title', 'customer_details.*');
        if ($request->filled('squery')) $query = $query->where('title', 'like', '%'.$request->squery.'%');
        $count = $query->count();
        $widget_requests = $query->paginate(env('ITEMS_PER_PAGE'));
        if ($request->excel_export == 0) {
        return view('admin.home-widgets.requests', compact('widget_requests', 'count'));
    } else {
        try {
          $fileLocation = public_path('export/WidgetRequests.xlsx');
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $styleArray = [ 'font' => [ 'bold' => true ] ];
          $sheet->getStyle('A1:K1')->applyFromArray($styleArray);
          $alphas = range('A', 'K');
          $headers = array(
            'Widget Name', 'Customer Name' , 'Customer Mobile',  'Comments', 'Alt Mobile', 'Village', 'Landmark', 'City' ,'State' , 'Pincode', 'Created At' );
          foreach ($headers as $key => $header) {
            $sheet->setCellValue($alphas[$key] . '1', $header);
          }
          $rows = 2;
          foreach ($widget_requests as $widget_request) {
              $sheet->setCellValue('A' . $rows, $widget_request->title );
              $sheet->setCellValue('B' . $rows, $widget_request->name);

              $sheet->setCellValue('C' . $rows, $widget_request->mobile);
              
              $sheet->setCellValue('D' . $rows, $widget_request->comments);
              if(!empty($widget_request->alt_mobile) || $widget_request->alt_mobile != NULL) {
                $mobile = $widget_request->alt_mobile;
              } else {
                $mobile = '';
              }
              $sheet->setCellValue('E' . $rows, $mobile);
              if(!empty($widget_request->village) || $widget_request->village != NULL) {
                $village = $widget_request->village;
              } else {
                $village = '';
              }
              $sheet->setCellValue('F' . $rows, $village);
              if(!empty($widget_request->landmark) || $widget_request->landmark != NULL) {
                $landmark = $widget_request->landmark;
              } else {
                $landmark = '';
              }
              $sheet->setCellValue('G' . $rows, $landmark);
              if(!empty($widget_request->city) || $widget_request->city != NULL) {
                $city = $widget_request->city;
              } else {
                $city = '';
              }
              $sheet->setCellValue('H' . $rows, $city);
              if(!empty($widget_request->state) || $widget_request->state != NULL) {
                $state = $widget_request->state;
              } else {
                $state = '';
              }
              $sheet->setCellValue('I' . $rows, $state);
              if(!empty($widget_request->pincode) || $widget_request->pincode != NULL) {
                $pincode = $widget_request->pincode;
              } else {
                $pincode = '';
              }
              $sheet->setCellValue('J' . $rows, $pincode);
              // if(!empty($widget_request->created_at) || $widget_request->created_at != NULL) {
              //   $created_at = Carbon::createFromDate($widget_request->created_at)->format('F d, Y');
              // } else {
              //   $created_at = "";
              // }
              $sheet->setCellValue('K' . $rows, Carbon::createFromDate($widget_request->created_at)->format('d/m/Y g:i A'));
      
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

  public function widget_request_delete(Request $request, $id) {
      WidgetRequest::find($id)->delete();
      Session::flash('state', 'Widget request deleted successfully');
      return redirect('admin/widgets/requests');
  }
}
