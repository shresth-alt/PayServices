@extends('admin.layouts.master')
@section("title")
Customers - Dashboard
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">TOTAL CUSTOMERS</span>
        <span class="badge badge-primary badge-pill animated flipInX">2</span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
  </div>
</div>
<div class="content">
  <form action="" method="GET">
    <div class="form-group form-group-feedback form-group-feedback-right search-box">
      <input type="text" class="form-control form-control-lg search-input"
      placeholder="Search with customer name..." name="query">
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
              <th style="width: 25%">Mobile</th>
              <th style="width: 20%;">Type</th>
              <th style="width: 15%;">Wallet Balance</th>
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
                  <a href="{{ route('editcustomer') }}" class="badge badge-primary badge-icon"> Edit <i class="icon-database-edit2 ml-1"></i></a>
                  <a href="" class="badge badge-danger badge-icon ml-1 doubleClickDelete" data-popup="tooltip" title="Double click to delete" data-placement="bottom"> <i class="icon-trash"></i> </a>
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
@endsection