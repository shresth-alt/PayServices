@extends('admin.layouts.master')
@section("title") Update Customer - Dashboard
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Editing</span>
        <span class="badge badge-primary badge-pill animated flipInX">"{{ $data->name }}"</span>
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
          <form action="{{ route('customer.update', $data->id) }}" method="POST" enctype="multipart/form-data">
          <input name="_method" type="hidden" value="PUT">
          @csrf
            <legend class="font-weight-semibold text-uppercase font-size-sm">
              <i class="icon-address-book mr-2"></i> Customer Details
            </legend>
            <input type="hidden" name="id" value="">
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Name:</label>
              <div class="col-lg-9">
                <input value="{{ $data->name }}" type="text" class="form-control form-control-lg" name="name"
                placeholder="Vendor Name" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Mobile:</label>
              <div class="col-lg-9">
                <input value="{{ $data->mobile }}" type="text" class="form-control form-control-lg" name="mobile"
                placeholder="Mobile" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Wallet Balance:</label>
              <div class="col-lg-9">
                <input value="{{ $data->wallet_balance }}" type="text" class="form-control form-control-lg" name="wallet" placeholder="Wallet Balance" required>
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
  </div>
</div>
@endsection