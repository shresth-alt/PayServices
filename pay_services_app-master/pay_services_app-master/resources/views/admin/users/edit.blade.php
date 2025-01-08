@extends('admin.layouts.master')
@section("title") Update User | Dashboard
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Editing</span>
        <span class="badge badge-primary badge-pill animated flipInX">"{{ $user->name }}"</span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
  </div>
</div>
<div class="content">
  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-body">
          <form class="jquery-validation-form" action="{{ route('user.update', $user->id) }}" method="POST"
            enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PUT">
            @csrf
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Name:</label>
              <div class="col-lg-9">
                <input value="{{filled(old('name')) ? old('name') : $user->name }}" type="text"
                  class="form-control form-control-lg" name="name" placeholder="Vendor Name" required>
                @error('name')<label id="type-error" class="error" for="type">{{ $message }}</label>@enderror
              </div>
            </div>
            @if ($user->type == 'manager' )
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Role:</label>
              <div class="col-lg-9">
                <select class="form-control select-search error" name="type" id="type" value="{{ old('type') }}">
                  <option value="">Select user type</option>
                  <option value="admin" @if (filled(old('type'))) {{old('type') == 'admin' ? 'selected' : ''}}
                    @else{{ $user->type == 'admin' ? 'selected' : ''}} @endif class="text-capitalize">Admin</option>
                  <option value="manager" @if (filled(old('type'))) {{old('type') == 'manager' ? 'selected' : ''}}
                    @else{{ $user->type == 'manager' ? 'selected' : ''}} @endif class="text-capitalize">Manager</option>
                </select>
              </div>
            </div>
            @endif
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Mobile:</label>
              <div class="col-lg-9">
                <input value="{{filled(old('mobile')) ? old('mobile') : $user->mobile}}" type="text"
                  class="form-control form-control-lg" name="mobile" placeholder="Mobile" required>
                @error('mobile')<label id="type-error" class="error" for="type">{{ $message }}</label>@enderror
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Email:</label>
              <div class="col-lg-9">
                <input value="{{ filled(old('email')) ? old('email') : $user->email }}" type="text"
                  class="form-control form-control-lg" name="email" placeholder="Email" required>
                @error('email')<label id="type-error" class="error" for="type">{{ $message }}</label>@enderror
              </div>
            </div>

            
            @if ($user->type == 'admin' || $user->type == 'manager' )
            @if ($current_user->type == 'admin')
              <div class="form-group row">
                <label class="col-lg-3 col-form-label">Not Permissions</label>
                <div class="col-lg-9 service-box">
                  <select multiple="multiple" name="permissions[]" data-placeholder="Select permissions"
                    class="form-control form-control-lg select2" data-container-css-class="select-lg" data-fouc>
                    <option value="view_dashboard" {{in_array('view_dashboard', $user_permission) ? 'selected' : '' }}  class="text-capitalize">View Dashboard</option>
                    <option value="view_service" {{in_array('view_service', $user_permission) ? 'selected' : '' }}  class="text-capitalize">View Service</option>
                    <option value="add_service" {{in_array('add_service', $user_permission) ? 'selected' : '' }}  class="text-capitalize">Add Service</option>
                    <option value="edit_service" {{in_array('edit_service', $user_permission) ? 'selected' : '' }}  class="text-capitalize">Edit Service</option>

                    <option value="view_home_widgets" {{in_array('view_home_widgets', $user_permission) ? 'selected' : '' }}   class="text-capitalize">View Home Widgets</option>
                    <option value="add_home_widgets" {{in_array('add_home_widgets', $user_permission) ? 'selected' : '' }}  class="text-capitalize">Add Home Widgets</option>
                    <option value="edit_home_widgets" {{in_array('edit_home_widgets', $user_permission) ? 'selected' : '' }}  class="text-capitalize">Edit Home Widgets</option>

                    <option value="view_requests" {{in_array('view_requests', $user_permission) ? 'selected' : '' }}  class="text-capitalize">View Requests</option>

                    <option value="view_user" {{in_array('view_user', $user_permission) ? 'selected' : '' }}  class="text-capitalize">View User</option>
                    <option value="add_user" {{in_array('add_user', $user_permission) ? 'selected' : '' }}  class="text-capitalize">Add User</option>
                    <option value="edit_user" {{in_array('edit_user', $user_permission) ? 'selected' : '' }}  class="text-capitalize">Edit User</option>

                    <option value="view_order" {{in_array('view_order', $user_permission) ? 'selected' : '' }}  class="text-capitalize">View Order</option>
                    <option value="edit_order" {{in_array('edit_order', $user_permission) ? 'selected' : '' }}   class="text-capitalize">Edit Order</option>
                    <option value="delete_order" {{in_array('delete_order', $user_permission) ? 'selected' : '' }}   class="text-capitalize">Delete Order</option>

                    <option value="view_accounts" {{in_array('view_accounts', $user_permission) ? 'selected' : '' }}  class="text-capitalize">View Accounts</option>

                  </select>
                </div>
              </div>
            @endif
            <div class="form-group row form-group-feedback form-group-feedback-right">
              <label class="col-lg-3 col-form-label">Password:</label>
              <div class="col-lg-9">
                <input id="passwordInput" type="password" class="form-control form-control-lg" name="password"
                  placeholder="Enter Password or keep blank if you don't want to change" autocomplete="new-password">
              </div>
            </div>
            @endif
            @if ($user->type == 'vendor')
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Wallet Balance:</label>
              <div class="col-lg-9">
                <input value="{{ $user->wallet_balance }}" type="text" class="form-control form-control-lg"
                  name="wallet" placeholder="Wallet Balance" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Pincodes:</label>
              <div class="col-lg-9">
                <input value="{{ implode(',', $pincodes) }}" type="text" class="form-control form-control-lg"
                  name="pincodes" placeholder="Pincodes separated by comma" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Services</label>
              <div class="col-lg-9 service-box">
                <select multiple="multiple" name="services[]" data-placeholder="Select services"
                  class="form-control form-control-lg select2" data-container-css-class="select-lg" data-fouc>
                  @foreach ($services as $service)
                  <option {{ in_array($service->id, $vendor_services) ? 'selected' : '' }} value="{{ $service->id }}">
                    {{ $service->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @endif
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Status</label>
              <div class="col-lg-9">
                <div class="checkbox checkbox-switchery mt-2">
                  <label>
                    <input value="{{ $user->is_active }}" type="checkbox" class="switchery-primary"
                      {{ $user->is_active ? 'checked' : '' }} name="is_active" data-switchery="true" />
                  </label>
                </div>
              </div>
            </div>
            @if ($user->type == 'vendor')
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Verified</label>
              <div class="col-lg-9">
                <div class="checkbox checkbox-switchery mt-2">
                  <label>
                    <input value="{{ $user->is_verified }}" type="checkbox" name="is_verified" data-switchery="true"
                      class="switchery-primary" {{ $user->is_verified ? 'checked' : '' }} />
                  </label>
                </div>
              </div>
            </div>
            @endif
            <div class="text-right">
              <button type="submit" class="btn btn-primary">
                UPDATE
                <i class="icon-database-insert ml-1"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @if (!empty($details))
    <div class="col-md-5">
      <div class="card">
        <div class="card-body">
        <form class="jquery-validation-form" action="{{ route('user.document', $user->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label>Father Name</label>
              <input readonly type="text" class="form-control" value="{{ $details->father_name }}">
            </div>
            <div class="form-group">
              <label>DOB</label>
              <input readonly type="text" class="form-control" value="{{ $details->dob }}">
            </div>
            <div class="form-group">
              <label>Alternate Mobile</label>
              <input readonly type="text" class="form-control" value="{{ $details->alt_mobile }}">
            </div>
            <div class="form-group">
              <label>Current Address</label>
              <input readonly type="text" class="form-control" value="{{ $details->cur_address }}">
            </div>
            <div class="form-group">
              <label>Permanent Address</label>
              <input readonly type="text" class="form-control" value="{{ $details->per_address }}">
            </div>
            <div class="form-group">
              <label>Village</label>
              <input readonly type="text" class="form-control" value="{{ $details->village }}">
            </div>
            <div class="form-group">
              <label>Town</label>
              <input readonly type="text" class="form-control" value="{{ $details->town }}">
            </div>
            <div class="form-group">
              <label>City</label>
              <input readonly type="text" class="form-control" value="{{ $details->city }}">
            </div>
            <div class="form-group">
              <label>District</label>
              <input readonly type="text" class="form-control" value="{{ $details->district }}">
            </div>
            <div class="form-group">
              <label>Pincode</label>
              <input readonly type="text" class="form-control" value="{{ $details->pincode }}">
            </div>
            <div class="form-group">
              <label>Qualification</label>
              <input readonly type="text" class="form-control" value="{{ $details->qualification }}">
            </div>
            <div class="form-group">
              <label>Aadhar Front:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" class="form-control-lg form-control-uniform error" name="aadhar_front"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readFront(this);">
                  <span class="filename front" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Aadhar Back:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" class="form-control-lg form-control-uniform error" name="aadhar_back"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readBack(this);">
                  <span class="filename back" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Driving License:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" class="form-control-lg form-control-uniform error" name="driving_license"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readLicense(this);">
                  <span class="filename license" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Pan Card:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" value="" class="form-control-lg form-control-uniform error" name="pan_card"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readPan(this);">
                  <span class="filename pan" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Photo:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" value="" class="form-control-lg form-control-uniform error" name="photo"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readPhoto(this);">
                  <span class="filename photo" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Cheque:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" value="" class="form-control-lg form-control-uniform error" name="cheque"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readCheque(this);">
                  <span class="filename cheque" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Signature:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" value="" class="form-control-lg form-control-uniform error" name="signature"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readSignature(this);">
                  <span class="filename signature" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Insurance:</label>
              <div class="uploader">
                <div class="uniform-uploader">
                  <input type="file" value="" class="form-control-lg form-control-uniform error" name="insurance"
                    accept="image/x-png,image/gif,image/jpeg" onchange="readInsurance(this);">
                  <span class="filename insurance" style="user-select: none;">No file selected</span>
                  <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->aadhar_front) }}">
                  <img src="{{ asset('uploads/'.$details->aadhar_front) }}" class="card-img-top img-fluid"
                      alt="..." /></a>
                    
                  <div class="card-body">
                    <h5 class="card-title">Aadhar Front</h5>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->aadhar_back) }}"><img
                      src="{{ asset('uploads/'.$details->aadhar_back) }}" class="card-img-top img-fluid"
                      alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Aadhar Back</h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="card">
                  <a href="{{ asset('uploads/'.$details->driving_license) }}" target="_blank"><img
                      src="{{ asset('uploads/'.$details->driving_license) }}" class="card-img-top img-fluid"
                      alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Driving License</h5>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->pan_card) }}"><img
                      src="{{ asset('uploads/'.$details->pan_card) }}" class="card-img-top img-fluid" alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Pan Card</h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->photo) }}"><img
                      src="{{ asset('uploads/'.$details->photo) }}" class="card-img-top img-fluid" alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Photo</h5>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->cheque) }}"><img
                      src="{{ asset('uploads/'.$details->cheque) }}" class="card-img-top img-fluid" alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Cheque</h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->signature) }}"><img
                      src="{{ asset('uploads/'.$details->signature) }}" class="card-img-top img-fluid" alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Signature</h5>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card">
                  <a target="_blank" href="{{ asset('uploads/'.$details->insurance) }}"><img
                      src="{{ asset('uploads/'.$details->insurance) }}" class="card-img-top img-fluid" alt="..." /></a>
                  <div class="card-body">
                    <h5 class="card-title">Insurance</h5>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-right">
            <button type="submit" class="btn btn-primary">
              UPDATE
              <i class="icon-database-insert ml-1"></i>
            </button>
          </div>
          </form>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    $('.select2').select2();
  });
  function readFront(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.front').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readBack(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.back').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readLicense(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.license').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readPan(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.pan').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readPhoto(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.photo').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readCheque(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.cheque').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readSignature(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.signature').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readInsurance(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.insurance').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  
</script>
@endsection