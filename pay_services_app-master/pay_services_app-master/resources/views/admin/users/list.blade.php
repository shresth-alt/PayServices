@extends('admin.layouts.master')
@section("title") Users | {{ env('APP_NAME') }} @endsection
@section('content')
<br>
<div class="page-header">
  @if(Session::get('state'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{Session::get('state')}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">TOTAL USERS</span>
        <span class="badge badge-primary badge-pill animated flipInX">{{ $users_count }}</span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
    <div class="header-elements d-none py-0 mb-3 mb-md-0">
      <div class="breadcrumb">
        <a href="{{ route('user.add') }}">
          <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2">
            <b><i class="icon-plus2"></i></b>
            Add User
          </button>
        </a>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form id='user_filters' action="{{route('users')}}" autocomplete="off" method="GET">
    <input type="hidden" value='0' name="excel_export" />
    <input type="hidden" value='{{$type}}' name="type" />
    <div class="form-group row template mt-2">
      <div class="col-lg-3">
        <label class="col-form-label" for="">Search:</label>
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
          <input type="text" class="form-control form-control-lg " placeholder="Search with user name or mobile"
          name="squery" value="{{ request('squery') }}">
          <div class="form-control-feedback form-control-feedback-lg mt-0">
            <i class="icon-search4"></i>
          </div>
        </div>
      </div>
      <div class="col-lg-3">
        <label class="col-form-label" for="">From Date:</label>
        <div class="input-group">
          <span class="input-group-prepend">
            <span class="input-group-text">
              <i class="icon-calendar"></i>
            </span>
          </span>
          <input value="{{$filters['date_from']}}" name="date_from" type="text"
          class="form-control form-control-lg datepicker" placeholder="Pick a date&hellip;">
        </div>
      </div>
      <div class="col-lg-3">
        <label class="col-form-label" for="">To Date:</label>
        <div class="input-group">
          <span class="input-group-prepend">
            <span class="input-group-text">
              <i class="icon-calendar"></i>
            </span>
          </span>
          <input value="{{$filters['date_to']}}" name="date_to" type="text"
          class="form-control form-control-lg datepicker" placeholder="Pick a date&hellip;">
        </div>
      </div>
      <div class="col-lg-3 submit-button-link">
        <button type="submit" class="btn btn-primary btn-icon" style="margin-left:0;"><i
          class="icon-search4"></i></button>
          <button type="button" id="clear_form" class="btn alpha-pink text-pink-800 btn-icon ml-2"><i
            class="icon-cross3"></i></button>
            @if (!empty($type))
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="excel_export"><b><i class="icon-file-excel"></i></b>Export</button>
            @endif
          </div>
        </div>
      </form>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Mobile</th>
                  <th>Type</th>
                  <th>Email</th>
                  <th>User Registration Date</th>
                  <th>W. Balance</th>
                  <th>M. Fee</th>
                  <th>Active</th>
                  <th>Verified</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($users as $user)
                <tr>
                  <td>
                    @if ($user->type != 'customer')
                    <a href="{{ route('user.edit', $user->user_id) }}">{{ $user->name }}</a>
                    @else
                    {{ $user->name }}
                    @endif
                  </td>
                  <td>{{ $user->mobile }}</td>
                  <td>{{ ucfirst($user->type) }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ Carbon\Carbon::createFromDate($user->registration_date)->format('d/m/Y g:i A')}}</td>
                  <td>{{ $user->wallet_balance }}</td> 
                  <td>{{ $user->membership_fee }}</td>
                  <td><span
                    class="badge badge-flat border-grey-800 text-default text-capitalize">{{ $user->is_active ? 'Yes' : 'No' }}</span>
                  </td>
                  <td>
                    @if ($user->type == 'manager' && $user->type == 'customer')
                    <span class="text-default"> - </span>
                    @else
                    <span
                    class="badge badge-flat border-grey-800 text-default text-capitalize">{{ $user->is_verified ? 'Yes' : 'No' }}
                  </span>
                  @endif
                </td>
              </tr>
              @endforeach
              @if (count($users) == 0)
              <tr>
                <td colspan="7" class="text-center">No results found</td>
              </tr>
              @endif
            </tbody>
          </table>
          <div class="mt-3">
            {{ $users->appends(request()->except('page'))->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  @endsection
  @section('scripts')
  <script type="text/javascript">
    $(document).ready(function () {
      var form = $('#user_filters');
      $('#clear_form').click(function () {
        form.find('input').val('');
        form.find('select').val('');
        form.submit();
      });
      $('#excel_export').click(function () {
        form.find("input[name='excel_export']").val(1);
        form.attr('target', '_blank').submit();
        form.removeAttr('target');
        form.find("input[name='excel_export']").val(0);
      });
    });
  </script>
  @endsection