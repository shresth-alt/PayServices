@extends('admin.layouts.master')
@section("title") Edit Order - Dashboard @endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Editing</span>
        <span class="badge badge-primary badge-pill animated flipInX">Order #{{ $order->id }}</span>
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
          <form action="{{ route('order.update', $order->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PUT">
            @csrf
            <legend class="font-weight-semibold text-uppercase font-size-sm">
              <i class="icon-address-book mr-2"></i> Enter Details
            </legend>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Vendor:</label>
              <div class="col-lg-9">
                <select class="form-control select-search" name="vendor_id">
                  <option value="0">Select Vendor</option>
                  @foreach ($vendors as $vendor)
                  <option value="{{ $vendor->id }}" {{ $vendor->id == $order->vendor_id ? 'selected' : '' }} class="text-capitalize">{{ $vendor->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Status:</label>
              <div class="col-lg-9">
                <select class="form-control select-search" name="status">
                  <option value="0">Select Status</option>
                  <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }} class="text-capitalize">Cancel Order</option>
                </select>
              </div>
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-primary">Update Order<i class="icon-database-insert ml-1"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection