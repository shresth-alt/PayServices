@extends('admin.layouts.master')
@section("title") Add User @endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Add User</span>
        <span class="badge badge-primary badge-pill animated flipInX"></span>
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
        <form class="jquery-validation-form" action="{{ route('user.post') }}" id="create_user" method="POST">
            @csrf
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Name:</label>
              <div class="col-lg-9">
                <input value="{{ old('name') }}" type="text" class="form-control form-control-lg" name="name"
                placeholder="Name" >
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Role:</label>
              <div class="col-lg-9">
                <select class="form-control select-search error" name="type" id="type" value="{{ old('type') }}">
                  <option value="">Select user type</option>
                  <option value="admin" {{ old('type') == 'admin' ? 'selected' : ''}} class="text-capitalize">Admin</option>
                  <option value="manager" {{ old('type') == 'manager' ? 'selected' : ''}} class="text-capitalize">Manager</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Mobile:</label>
              <div class="col-lg-9">
                <input value="{{ old('mobile') }}" type="text" class="form-control form-control-lg" name="mobile"
                placeholder="Mobile" >
                @error('mobile')<label id="type-error" class="error" for="type">{{ $message }}</label>@enderror
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Email:</label>
              <div class="col-lg-9">
                <input value="{{ old('email') }}" type="text" class="form-control form-control-lg" name="email"
                placeholder="Email" >
                @error('email')<label id="type-error" class="error" for="type">{{ $message }}</label>@enderror
              </div>
            </div>
            <div class="form-group row hidden" id="permissions">
              <label class="col-lg-3 col-form-label">Permissions</label>
              <div class="col-lg-9 service-box">
                <select multiple="multiple" name="permissions[]" data-placeholder="Select permissions"
                  class="form-control form-control-lg select2" data-container-css-class="select-lg" data-fouc>
                  <option value="view_dashboard"   class="text-capitalize">View Dashboard</option>
                  <option value="view_service"  class="text-capitalize">View Service</option>
                  <option value="add_service"   class="text-capitalize">Add Service</option>
                  <option value="edit_service"   class="text-capitalize">Edit Service</option>

                  <option value="view_home_widgets" class="text-capitalize">View Home Widgets</option>
                  <option value="add_home_widgets" class="text-capitalize">Add Home Widgets</option>
                  <option value="edit_home_widgets" class="text-capitalize">Edit Home Widgets</option>

                  <option value="view_requests" class="text-capitalize">View Requests</option>

                  <option value="view_user" class="text-capitalize">View User</option>
                  <option value="add_user" class="text-capitalize">Add User</option>
                  <option value="edit_user" class="text-capitalize">Edit User</option>

                  <option value="view_order" class="text-capitalize">View Order</option>
                  <option value="edit_order" class="text-capitalize">Edit Order</option>

                  <option value="view_accounts" class="text-capitalize">View Accounts</option>

                </select>
              </div>
            </div>
            <div class="form-group row form-group-feedback form-group-feedback-right">
              <label class="col-lg-3 col-form-label">Password:</label>
              <div class="col-lg-9">
                <input id="passwordInput" type="password" class="form-control form-control-lg" name="password" placeholder="Enter Password" autocomplete="new-password">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Status</label>
              <div class="col-lg-9">
                <div class="checkbox checkbox-switchery mt-2">
                  <label>
                    <input value="" type="checkbox" class="switchery-primary" name="is_active"
                    data-switchery="true" />
                  </label>
                </div>
              </div>
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-primary">
                Add User
                <i class="icon-database-insert ml-1"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $(document).ready(function() {
    $('#create_user').validate({
      rules: {
        name: "required",
        type: "required",
        mobile: "required",
        email: "required",
        password: "required",
        }
      ,
      messages: {
        name: "Please enter User name",
        type: "Please select User type",
        mobile: "Please enter mobile no.",
        email: "Please enter email",
        password: "Please enter password",
      }
    });
    
    $('.select2').select2();
    $("#type").change(function() {
      var type = $(this).val();
      if (type == "manager") {
        $("#permissions").removeClass('hidden');
        $('.select2').select2('reload');
      } else {
        $("#permissions").addClass('hidden');
      }
    });

  });
</script>
@endsection