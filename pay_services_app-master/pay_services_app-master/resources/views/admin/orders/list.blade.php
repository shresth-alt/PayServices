@extends('admin.layouts.master')
@section("title")
Orders - Dashboard
@endsection
@section('content')
<div class="page-content container pt-0">
  <div class="content-wrapper">
    <div class="page-header">
      <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
          <h4><i class="icon-circle-right2 mr-2"></i>
            <span class="font-weight-bold mr-2">TOTAL</span>
            <span class="badge badge-primary badge-pill animated flipInX">{{ $count }}</span>
          </h4>
          <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
      </div>
    </div>
    <div class="content">
      <form id="order_filters" action="{{route('orders.list')}}" autocomplete="off" method="GET">
        <input type="hidden" value='0' name="excel_export" />
        <div class="form-group row template mt-2">
          <div class="col-lg-2">
            <label class="col-form-label" for="">Search:</label>
            
            <div class="form-group form-group-feedback form-group-feedback-right search-box">
              <input type="text" class="form-control form-control-lg " placeholder="#ID, Customer Name, Customer Mobile" name="squery"
                value="{{ request('squery') }}">
              <div class="form-control-feedback form-control-feedback-lg mt-0">
                <i class="icon-search4"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-3">
            <label class="col-form-label" for="">Order status:</label>
              <select class="form-control select-search order-status" name="status" id="status">
                <option class="status-label" value="">Select order status</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }} class="text-capitalize">Completed</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} class="text-capitalize">Pending</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }} class="text-capitalize">Cancelled</option>
              </select>
            </div>
          <div class="col-lg-2">
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
          <div class="col-lg-2">
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
            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="excel_export"><b><i class="icon-file-excel"></i></b>Export</button>
          </div>
        </div>
      </form>
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Order#</th>
                  <th>Customer Name</th>
                  <th>Customer Mobile </th>
                  <th>Vendor Name</th>
                  <th>Vendor Mobile</th>
                  <th>Service</th>
                  <th>Service Date</th>
                  <th>Status</th>
                  <th>Pending Status</th>
                  <th>Order Status Date</th>
                  <th>Payment Status</th>
                  <th>Order Amount</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($orders as $order)
                <tr>
                  <td>{{ $order->order_id }}</td>
                  <td>{{ $order->customer_name }}</td>
                  <td>@if (!empty($order->user_mobile)) {{$order->user_mobile}} @endif</td>
                  <td>{{ $order->vendor_name }}</td>
                  <td> {{$order->vendor_mobile}} </td>
                  <td>{{ $order->service_name }}</td>
                  <td> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->service_date)->format('d/m/Y g:i A')}}</td>
                  <td>{{ ucfirst($order->status) }}</td>
                  <td>@if(!empty($order->comments)){{ $order->comments }} @else NA @endif</td>
                  <td>@if(!empty($order->order_status_date)) {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $order->order_status_date)->format('d/m/Y g:i A')}} @endif </td>
                  <td>@if($order->payment_status != 'na') {{ucfirst($order->payment_status)}} @else -- @endif</td>
                  <td>{{ $order->order_amount }}</td>
                  <td class="text-center">
                    <div class="btn-group btn-group-justified">
                      @if ($order->status == 'completed')
                      <a href="{{ route('save.invoice', $order->order_id)}}" class="badge badge-secondary badge-icon ml-1">
                        Invoice <i class="icon-arrow-right5 ml-1"></i> </a>
                      @else
                      <button disabled class="disable-button btn-primary badge badge-icon ml-1 ">
                        Invoice <i class="icon-arrow-right5 ml-1"></i> </button>
                      @endif
                      <a href="{{ route('orders.order', $order->order_id)}}" class="badge badge-info badge-icon ml-1"> Details
                        <i class="icon-arrow-right5 ml-1"></i> </a>
                      @if ($order->status == 'cancelled')
                      <button disabled class="disable-button badge badge-primary badge-icon ml-1"> Edit
                        Order <i class="icon-arrow-right5 ml-1"></i></button>
                      @else
                      <a href="{{ route('order.edit', $order->order_id) }}" class="badge badge-primary badge-icon ml-1"> Edit
                        Order <i class="icon-arrow-right5 ml-1"></i></a>
                      @endif
                      <a href="{{route('order.delete', $order->order_id )}}" class="badge badge-danger badge-icon ml-1 " data-popup="tooltip"
                        title="" data-placement="bottom"> <i class="icon-trash"></i> </a>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="11" class="text-center">No results found</td>
                </tr>
                @endforelse
              </tbody>
            </table>
            <div>
              {{ $orders->appends(request()->except('page'))->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    var form = $('#order_filters');
    $('#clear_form').click(function () {
      form.find('input').val('');
      form.find('select').val('');
      form.submit();
    });
    $('#excel_export').click(function() {
      form.find("input[name='excel_export']").val(1);
      form.attr('target', '_blank').submit();
      form.removeAttr('target');
      form.find("input[name='excel_export']").val(0);
    });
  });
  
</script>
@endsection
