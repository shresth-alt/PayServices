@extends('admin.layouts.master')
@section("title")
Order - Dashboard
@endsection
@section('content')
<div class="page-content container pt-0">
  <div class="content-wrapper">
    <div class="page-header">
      <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
          <h4><i class="icon-circle-right2 mr-2"></i>
            <span class="font-weight-bold mr-2">TOTAL DETAIL</span>
            <span class="badge badge-primary badge-pill animated flipInX">0</span>
          </h4>
          <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
      </div>
    </div>
    <div class="content">
      <form action="" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
          <input type="text" class="form-control form-control-lg search-input" placeholder="Search with order id..." name="query">
          <div class="form-control-feedback form-control-feedback-lg">
            <i class="icon-search4"></i>
          </div>
        </div>
        <input type="hidden" name="_token" value=""> 
      </form>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Order Id</th>
                  <th>Which Service</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1234</td>
                  <td>Plumber</td>
                  <td>₹12</td>
                </tr>
              </tbody>
            </table>
            <div class="mt-3">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection