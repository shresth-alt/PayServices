@extends('admin.layouts.master')
@section("title")
Customers - Dashboard
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">TOTAL CUSTOMER</span>
        <span class="badge badge-primary badge-pill animated flipInX">{{ $total }}</span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
    <!-- <div class="header-elements d-none py-0 mb-3 mb-md-0">
      <div class="breadcrumb">  
        <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewRestaurant"
        data-toggle="modal" data-target="#addNewRestaurantModal">
        <b><i class="icon-plus2"></i></b>
        Add New Vendor
      </button>
    </div>
  </div> -->
</div>
</div>
<div class="content">
  <form action="" method="GET">
    <div class="form-group form-group-feedback form-group-feedback-right search-box">
      <input type="text" class="form-control form-control-lg search-input"
      placeholder="Search with vendor name..." name="query">
      <div class="form-control-feedback form-control-feedback-lg">
        <i class="icon-search4"></i>
      </div>
    </div>
  </form>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="width: 25%;">Name</th>
              <th style="width: 20%">Mobile</th>
              <th style="width: 20%;">Type</th>
              <th style="width: 20%;">Wallet Balance</th>
              <th class="text-center" style="width: 10%;"><i class="icon-circle-down2"></i></th>
            </tr>
          </thead>
          <tbody>
          @foreach($customers as $customer)
            <tr>
              <td>{{ $customer->name }}</td>
              <td>{{ $customer->mobile }}</td>
              <td>{{ $customer->type }}</td>
              <td>&#8377;{{ $customer->wallet_balance }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-justified">
                  <a href="{{ route('customer.edit', $customer->id) }}" class="badge badge-primary badge-icon"> Edit <i class="icon-database-edit2 ml-1"></i></a>
                  <a href="{{ route('customer.delete', $customer->id) }}" class="badge badge-danger badge-icon ml-1 doubleClickDelete" data-popup="tooltip" title="Double click to delete" data-placement="bottom"> <i class="icon-trash"></i> </a>
                </div>
              </td>
            </tr> 
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- <div id="addNewRestaurantModal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class="font-weight-bold">Add New Vendor</span></h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="form-group row">
            <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Name:</label>
            <div class="col-lg-9">
              <input type="text" class="form-control form-control-lg" name="name" placeholder="Vendor Name"
              required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Mobile:</label>
            <div class="col-lg-9">
              <input type="text" class="form-control form-control-lg" name="mobile" placeholder="Mobile"
              required>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>User Type:</label>
            <div class="col-lg-9">
              <select name="role" class="form-control select"  tabindex="-1">
                <option value="" class="text-capitalize">Admin</option>
                <option value="" class="text-capitalize">Vendor</option>
                <option value="" class="text-capitalize">Customer</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Wallet Balance:</label>
            <div class="col-lg-9">
              <input type="text" class="form-control form-control-lg" name="wallet"
              placeholder="Wallet Balance" required>
            </div>
          </div>
          <div class="text-right">
            <button type="submit" class="btn btn-primary">
              SAVE
              <i class="icon-database-insert ml-1"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div> -->
  @endsection