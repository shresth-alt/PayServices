@extends('admin.layouts.master')
@section("title")
Dashboard
@endsection
@section('content')
<div class="content mb-5">
  <div id="update_notification" style="display:none;" class="alert alert-update mt-2">
    <button type="button" style="margin-left: 20px" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="row mt-4">
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-toggle"></i>
            </div>
            <div class="dashboard-display-number">{{ $services_count }}</div>
            <div class="font-size-sm text-uppercase text-muted">Services</div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-toggle"></i>
            </div>
            <div class="dashboard-display-number">{{ $widgets_count }}</div>
            <div class="font-size-sm text-uppercase text-muted">Home Widgets</div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-users4"></i>
            </div>
            <div class="dashboard-display-number">{{ $vendors_count }}</div>
            <div class="font-size-sm text-uppercase text-muted">Vendors</div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-users4"></i>
            </div>
            <div class="dashboard-display-number">{{ $customers_count }}</div>
            <div class="font-size-sm text-uppercase text-muted">Customers</div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-basket"></i>
            </div>
            <div class="dashboard-display-number">{{ $orders_count }}</div>
            <div class="font-size-sm text-uppercase text-muted">Orders</div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-basket"></i>
            </div>
            <div class="dashboard-display-number">{{$completed_orders}}</div>
            <div class="font-size-sm text-uppercase text-muted"><a href="{{route('orders.list')}}?excel_export=0&squery=&status=completed&date_from=&date_to="> Completed Orders </a></div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-basket"></i>
            </div>
            <div class="dashboard-display-number">{{$pending_orders}}</div>
            <div class="font-size-sm text-uppercase text-muted"><a href="{{route('orders.list')}}?excel_export=0&squery=&status=pending&date_from=&date_to=">Pending Orders </a></div>
          </div>
        </a>
      </div>
    </div>
    <div class="col-6 col-xl-3 mb-2 mt-2">
      <div class="col-xl-12 dashboard-display p-3">
        <a class="block block-link-shadow text-left" href="javascript:void(0)">
          <div class="block-content block-content-full clearfix">
            <div class="float-right mt-10 d-none d-sm-block">
              <i class="dashboard-display-icon icon-basket"></i>
            </div>
            <div class="dashboard-display-number">{{$cancelled_orders}}</div>
            <div class="font-size-sm text-uppercase text-muted"><a href="{{route('orders.list')}}?excel_export=0&squery=&status=cancelled&date_from=&date_to=">Cancelled Orders </a></div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>
@endsection