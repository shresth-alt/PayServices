<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;
use Session;
use Illuminate\Http\Request;
use App\VendorPincode;
use App\VendorService;
use App\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\VendorDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserController extends Controller {
  
  public function list(Request $request) {
    
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'view_user')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $filters = array(
        'date_from' => '',
        'date_to' => '',
      );
      $query = DB::table('users')->select('users.*', 'users.id as user_id', 'users.created_at as registration_date', 'vendor_details.*', 'vendor_details.alt_mobile as vendor_alt_mobile', 
      'vendor_details.village as vendor_village', 'vendor_details.town as vendor_town' , 'vendor_details.city as vendor_city', 'vendor_details.state as vendor_state', 'vendor_details.pincode as vendor_pincode', 'customer_details.*')
      ->leftJoin('vendor_details' , 'vendor_details.vendor_id', 'users.id')
      ->leftJoin('customer_details' , 'customer_details.customer_id', 'users.id');
      if ($request->filled('squery')) $query = $query->where('users.name', 'like', '%'.$request->squery.'%')->orWhere('users.mobile', 'like', '%'.$request->squery.'%')->orWhere('users.type', 'like', '%'.$request->squery.'%');
      if ($request->filled('type')) $query = $query->where('users.type', $request->type);
      if ($request->filled('date_from') && $request->filled('date_to')) {
        $query = $query->whereDate('users.created_at', ">=", Carbon::createFromFormat('d/m/Y', $request->date_from)->format('Y-m-d'));
        $query = $query->whereDate('users.updated_at', "<=", Carbon::createFromFormat('d/m/Y', $request->date_to)->format('Y-m-d'));
        $filters['date_from'] = $request->date_from;
        $filters['date_to'] = $request->date_to;
      }
      $users_count = $query->count();
      $user_details = $query->orderBy('users.id', 'DESC')->get();
      $type = $request->type;
      $users = $query->paginate(env('ITEMS_PER_PAGE'));
      if ($request->excel_export == 0) {
        return view('admin.users.list', compact('users', 'users_count', 'filters', 'type'));
      } else { 
        try {
          $fileLocation = public_path('export/Users.xlsx');
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $styleArray = [ 'font' => [ 'bold' => true ] ];
          if ($request->type == 'vendor') {
            $sheet->getStyle('A1:V1')->applyFromArray($styleArray);
            $alphas = range('A', 'V');
            $headers = array(
              'Vendor ID', 'Name', 'Mobile' , 'Type' , 'Email', 'User Registration Date' , 'W. Balance', 'M. Fee', 'Active', 'Verified' , 'Father Name', 'Date of Birth', 'Alternate Mobile', 'Current address', 'Permanent address', 'Village', 'Town', 'City', 'State', 'District', 'Pincode', 'Qualification'
            );
            foreach ($headers as $key => $header) {
              $sheet->setCellValue($alphas[$key] . '1', $header);
            }
            $rows = 2;
            foreach ($user_details as $user) {
              $sheet->setCellValue('A' . $rows, $user->user_id);
              $sheet->setCellValue('B' . $rows, $user->name);
              $sheet->setCellValue('C' . $rows, $user->mobile);
              $sheet->setCellValue('D' . $rows, $user->type);
              $sheet->setCellValue('E' . $rows, $user->email);
              $sheet->setCellValue('F' . $rows, Carbon::createFromDate($user->registration_date)->format('d/m/Y g:i A'));
              $sheet->setCellValue('G' . $rows, $user->wallet_balance);
              $sheet->setCellValue('H' . $rows, $user->membership_fee);
              $sheet->setCellValue('I' . $rows,  $user->is_active ? 'Yes' : 'No');
              $sheet->setCellValue('J' . $rows, $user->is_verified ? 'Yes' : 'No');
              if (!empty($user->father_name)) {
                $father_name = $user->father_name;
              } else {
                $father_name = "";
              }
              $sheet->setCellValue('K' . $rows, $father_name);
              if (!empty($user->dob)) {
                $dob = $user->dob;
              } else {
                $dob = "";
              }
              $sheet->setCellValue('L' . $rows, $dob);
              if (!empty($user->vendor_alt_mobile)) {
                $vendor_alt_mobile = $user->vendor_alt_mobile;
              } else {
                $vendor_alt_mobile = "";
              }
              $sheet->setCellValue('M' . $rows, $vendor_alt_mobile);
              if (!empty($user->cur_address)) {
                $cur_address = $user->cur_address;
              } else {
                $cur_address = "";
              }
              $sheet->setCellValue('N' . $rows, $cur_address);
              if (!empty($user->per_address)) {
                $per_address = $user->per_address;
              } else {
                $per_address = "";
              }
              $sheet->setCellValue('O' . $rows, $per_address);
              if (!empty($user->vendor_village)) {
                $village = $user->vendor_village;
              } else {
                $village = "";
              }
              $sheet->setCellValue('P' . $rows, $village);
              if (!empty($user->vendor_town)) {
                $town = $user->vendor_town;
              } else {
                $town = "";
              }
              $sheet->setCellValue('Q' . $rows, $town);
              if (!empty($user->vendor_city)) {
                $city = $user->vendor_city;
              } else {
                $city = "";
              }
              $sheet->setCellValue('R' . $rows, $city);
              if (!empty($user->vendor_state)) {
                $state = $user->vendor_state;
              } else {
                $state = "";
              }
              $sheet->setCellValue('S' . $rows, $state);
              if (!empty($user->district)) {
                $district = $user->district;
              } else {
                $district = "";
              }
              $sheet->setCellValue('T' . $rows, $district);
              if (!empty($user->vendor_pincode)) {
                $pincode = $user->vendor_pincode;
              } else {
                $pincode = "";
              }
              $sheet->setCellValue('U' . $rows, $pincode);
              if (!empty($user->qualification)) {
                $qualification = $user->qualification;
              } else {
                $qualification = "";
              }
              $sheet->setCellValue('V' . $rows, $qualification);
              
              $rows++;
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileLocation);
            return response()->download($fileLocation);
          } elseif ($type == 'customer') {
            $sheet->getStyle('A1:O1')->applyFromArray($styleArray);
            $alphas = range('A', 'O');
            $headers = array(
              'Customer ID' , 'Name', 'Mobile', 'Altternate Mobile' , 'Type' , 'Email', 'User Registration Date' , 'W. Balance', 'M. Fee', 'Active', 'Village', 'Landmark', 'City', 'State', 'Pincode'
            );
            foreach ($headers as $key => $header) {
              $sheet->setCellValue($alphas[$key] . '1', $header);
            }
            $rows = 2;
            foreach ($user_details as $user) {
              $sheet->setCellValue('A' . $rows, $user->user_id);
              $sheet->setCellValue('B' . $rows, $user->name );
              $sheet->setCellValue('C' . $rows, $user->mobile);
              if (!empty($user->alt_mobile)) {
                $alt_mobile = $user->alt_mobile;
              } else {
                $alt_mobile = "";
              }
              $sheet->setCellValue('D' . $rows, $alt_mobile);
              $sheet->setCellValue('E' . $rows, $user->type);
              $sheet->setCellValue('F' . $rows, $user->email);
              $sheet->setCellValue('G' . $rows, Carbon::createFromDate($user->registration_date)->format('d/m/Y g:i A'));
              $sheet->setCellValue('H' . $rows, $user->wallet_balance);
              $sheet->setCellValue('I' . $rows, $user->membership_fee);
              $sheet->setCellValue('J' . $rows,  $user->is_active ? 'Yes' : 'No');
              if (!empty($user->village)) {
                $village = $user->village;
              } else {
                $village = "";
              }
              $sheet->setCellValue('K' . $rows, $village);
              if (!empty($user->landmark)) {
                $landmark = $user->landmark;
              } else {
                $landmark = "";
              }
              $sheet->setCellValue('L' . $rows, $landmark);
              if (!empty($user->city)) {
                $city = $user->city;
              } else {
                $city = "";
              }
              $sheet->setCellValue('M' . $rows, $city);
              if (!empty($user->state)) {
                $state = $user->state;
              } else {
                $state = "";
              }
              $sheet->setCellValue('N' . $rows, $state);
              if (!empty($user->pincode)) {
                $pincode = $user->pincode;
              } else {
                $pincode = "";
              }
              $sheet->setCellValue('O' . $rows, $pincode);
              $rows++;
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileLocation);
            return response()->download($fileLocation);
          } elseif ($type == 'manager') {
            $sheet->getStyle('A1:I1')->applyFromArray($styleArray);
            $alphas = range('A', 'I');
            $headers = array(
              'Manager ID', 'Name', 'Mobile' , 'Type' , 'Email', 'User Registration Date' , 'W. Balance', 'M. Fee', 'Active'
            );
            foreach ($headers as $key => $header) {
              $sheet->setCellValue($alphas[$key] . '1', $header);
            }
            $rows = 2;
            foreach ($user_details as $user) {
              $sheet->setCellValue('A' . $rows, $user->user_id);
              $sheet->setCellValue('B' . $rows, $user->name );
              $sheet->setCellValue('C' . $rows, $user->mobile);
              $sheet->setCellValue('D' . $rows, $user->type);
              $sheet->setCellValue('E' . $rows, $user->email);
              $sheet->setCellValue('F' . $rows, Carbon::createFromDate($user->registration_date)->format('d/m/Y g:i A'));
              $sheet->setCellValue('G' . $rows, $user->wallet_balance);
              $sheet->setCellValue('H' . $rows, $user->membership_fee);
              $sheet->setCellValue('I' . $rows,  $user->is_active ? 'Yes' : 'No');
              $rows++;
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save($fileLocation);
            return response()->download($fileLocation);
          }
        } catch(Exception $e) {
        }
        
      }
    }
  }
  
  public function addUser(Request $request) {
    $current_user_id = Auth::id();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'add_user')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      return view('admin.users.add');
    }
  }
  
  public function saveUser(Request $request) {
    $validation = Validator::make($request->all(), array(
      'name' => 'required',
      'type' => 'required',
      'email' => 'required|unique:users|email',
      'mobile' => 'required|unique:users|digits:10',
      'password' => 'required',
    ));
    if ($validation->fails()) {
      return redirect("/admin/users/add")->withInput()->withErrors($validation);
    } else {
      $user = User::create(array(
        'name' => $request->name,
        'type' => $request->type,
        'mobile' => $request->mobile,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'is_active' => $request->has('is_active') ? true : false
      ));
      
      if (!empty($request->permissions)) {
        // echo "<pre>";
        // print_r($user->id);
        // exit;
        foreach ($request->permissions as $key => $permission) {
          Permission::create(array(
            'user_id' => $user->id,
            'permission' => $permission,
          ));
        }
      }
    }
    $request->session()->flash('state', 'User added successfully');
    return redirect('admin/users');
  }
  
  public function editCustomer(Request $request, $id) {
    $data = User::find($id);
    $users = User::where('id', '<>', $id)->get();
    return view('admin.customers.edit', compact('data', 'users'));
  }
  
  /** Update the Vendor */
  public function updateCustomer(Request $request, $id) {
    $user = User::find($id);
    $user->name = $request->name;
    $user->mobile = $request->mobile;
    $user->wallet_balance = $request->wallet;
    $user->is_active = $request->has('is_active') ? true : false;
    $user->update();
    $request->session()->flash('state', 'Customer update successfully');
    return redirect('admin/customers');
  }
  
  /** Delete Services  */
  public function deleteCustomer($id) {
    User::find($id)->delete();
    Session::flash('state', 'Vendor deleted successfully');
    return redirect('admin/customers');
  }
  
  public function edit(Request $request, $id) {
    $current_user_id = Auth::id();
    $current_user = Auth::user();
    $permission = Permission::where('user_id', $current_user_id)->where('permission', 'edit_user')->first();
    if (!empty($permission)) {
      return redirect(route('unauthorized'));
    } else {
      $user = User::find($id);
      $pincodes = array();
      $vendor_services = array();
      $details = array();
      if ($user->type == 'vendor') {
        foreach ($user->pincodes as $pincode) {
          $pincodes[] = $pincode->pincode;
        }
        foreach ($user->services as $service) {
          $vendor_services[] = $service->service_id;
        }
        $details = $user->detail;
        if ($details == null) $details = array();
      }
      $user_permission[] = "";
      $permissions = Permission::where('user_id', $user->id)->select('permission')->get();
      foreach ($permissions as $permission) {
        $user_permission[] = $permission->permission;
      }
      $services = DB::select('select * from services');
      return view('admin.users.edit', compact('user', 'pincodes', 'services', 'vendor_services', 'details', 'user_permission', 'current_user'));
    }
  }
  
  /** Update the User */
  public function update(Request $request, $id) {
    $validation = Validator::make($request->all(), array(
      'name' => 'required',
      'email' => 'required|unique:users,email,' . $id,
      'mobile' => 'required|digits:10|unique:users,mobile,' . $id,
    ));
    if ($validation->fails()) {
      return redirect("/admin/users/".$id."/edit")->withInput()->withErrors($validation);
    } else {
      $user = User::find($id);
      $user->name = $request->name;
      $user->mobile = $request->mobile;
      $user->email = $request->email;
      $serv = $request->input('a');
      if ($user->type == 'admin' && $request->filled('password')) {
        $user->password = bcrypt($request->password);
      } else if ($user->type == 'vendor') {
        $user->wallet_balance = $request->wallet;
        $pincodes = explode(",", $request->pincodes);
        $user->pincodes()->delete();
        foreach ($pincodes as $pincode) {
          $pincode = VendorPincode::create(array(
            'vendor_id' => $user->id,
            'pincode' => trim($pincode)
          ));
        }
        /** Services of vendor */
        VendorService::where('vendor_id', $user->id)->delete();
        if (!empty($request->services)) {
          foreach ($request->services as $key => $service_id) {
            VendorService::create(array(
              'vendor_id' => $user->id,
              'service_id' => $service_id
            ));
          }
        }
      }
      // $abc = Permission::find(34);
      DB::table('permissions')->where('user_id', $id)->select('permissions.*')->delete();
      if (!empty($request->permissions)) {
        foreach ($request->permissions as $key => $permission) {
          Permission::create(array(
            'user_id' => $user->id,
            'permission' => $permission
          ));
        }
      }
      $user->is_active = $request->has('is_active') ? true : false;
      $user->is_verified = $request->has('is_verified') ? true : false;
      $user->update();
    }
    $request->session()->flash('state', 'User update successfully');
    if ($user->type == 'vendor') return redirect('admin/users?type=vendor');
    if ($user->type == 'customer') return redirect('admin/users?type=customer');
    return redirect('admin/users');
  }
  public function updateDocuments(Request $request, $id) {
    if ($request->hasFile('aadhar_front') && $request->file('aadhar_front')->isValid()) {
      $file = $request->aadhar_front;
      $extension = $request->file('aadhar_front')->extension();
      $file_name = time().'_aadhar_front.'. $extension;
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->aadhar_front = $file_name;
      $vendor_detail->update();
    }
    
    if ($request->hasFile('aadhar_back') && $request->file('aadhar_back')->isValid()) {
      $file = $request->aadhar_back;
      $file_name = time().'_aadhar_back.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->aadhar_back = $file_name;
      $vendor_detail->update();
    }
    if ($request->hasFile('driving_license') && $request->file('driving_license')->isValid()) {
      $file = $request->driving_license;
      $file_name = time().'_driving_license.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->driving_license = $file_name;
      $vendor_detail->update();
    }
    if ($request->hasFile('pan_card') && $request->file('pan_card')->isValid()) {
      $file = $request->pan_card;
      $file_name = time().'_pan_card.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->pan_card = $file_name;
      $vendor_detail->update();
    }
    if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
      $file = $request->photo;
      $file_name = time().'_photo.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->photo = $file_name;
      $vendor_detail->update();
    }
    if ($request->hasFile('cheque') && $request->file('cheque')->isValid()) {
      $file = $request->cheque;
      $file_name = time().'_cheque.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->cheque = $file_name;
      $vendor_detail->update();
    }
    if ($request->hasFile('signature') && $request->file('signature')->isValid()) {
      $file = $request->signature;
      $file_name = time().'_signature.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->signature = $file_name;
      $vendor_detail->update();
    }
    if ($request->hasFile('insurance') && $request->file('insurance')->isValid()) {
      $file = $request->insurance;
      $file_name = time().'_insurance.png';
      $destination_path = public_path('uploads');
      $file->move($destination_path,$file_name);
      $vendor_detail = VendorDetail::where('vendor_id', $id)->first();
      $vendor_detail->insurance = $file_name;
      $vendor_detail->update();
    }
    Session::flash('state', 'Document updated successfully');
    return redirect('admin/users?type=vendor');
  }
  
  /** Delete Services  */
  public function deleteVendor($id) {
    User::find($id)->delete();
    Session::flash('state', 'Vendor deleted successfully');
    return redirect('admin/vendors');
  }
  
  // List Services
  public function service(Request $request, $id) {
    $services = DB::select('select * from services');
    return view('admin.users.services', ['services' => $services, 'id'=>$id]);
  }
  
  public function add(Request $request ,$id){
    $value = $request->all();
    $data = $request->input('serv');
    foreach ($data as $value) {
      $vendor_service = new VendorService;
      $vendor_service->vendor_id = $id;
      $vendor_service->service_id = $value;
      $vendor_service->save();
    }
    Session::flash('state', 'New service added successfully');
    return redirect('admin/users');
  }
}
