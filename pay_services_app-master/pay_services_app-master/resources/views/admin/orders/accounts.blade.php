@extends('admin.layouts.master')
@section("title")
Order Accounts - Dashboard
@endsection
@section('content')
<div class="page-content container pt-0">
  <div class="content-wrapper">
    <div class="page-header">
      <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
          <h4><i class="icon-circle-right2 mr-2"></i>
            <span class="font-weight-bold mr-2">TOTAL</span>
            <span class="badge badge-primary badge-pill animated flipInX">{{$count}}</span>
          </h4>
        </div>
        <div class="page-title float-right">
          <h4>
            <span class="font-weight-bold mr-2">Total Profit: </span> <span> &#8377; {{ $total_profit }}</span> 	&nbsp;
            <span class="font-weight-bold mr-2">Total GST: </span> <span> &#8377; {{ $commission_gst }}</span>
          </h4>
        </div>
      </div>
    </div>
    <div class="content">
      <form id='accounts_filters' action="{{route('order.accounts')}}" autocomplete="off" method="GET">
        <input type="hidden" value='0' name="excel_export" />
        <div class="form-group row template mt-2">
          <div class="col-lg-3">
            <label class="col-form-label" for="">Search:</label>
            <div class="form-group form-group-feedback form-group-feedback-right search-box">
              <input type="text" class="form-control form-control-lg " placeholder="Order #ID, Name, Or Mobile" name="squery" value="{{ request('squery') }}">
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
              <input value="{{$filters['date_from']}}" name="date_from" type="text" class="form-control form-control-lg datepicker" placeholder="Pick a date&hellip;">
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
              <input value="{{ $filters['date_to'] }}" name="date_to" type="text" class="form-control form-control-lg datepicker" placeholder="Pick a date&hellip;">
            </div>
          </div>
          <div class="col-lg-3 submit-button-link">
            <button type="submit" class="btn btn-primary btn-icon" style="margin-left:0;"><i class="icon-search4"></i></button>
            <button type="button" id="clear_form" class="btn alpha-pink text-pink-800 btn-icon ml-2"><i class="icon-cross3"></i></button>
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
                  <th>Invoice#</th>
                  <th> Customer Name</th>
                  <th> Customer Address</th>
                  <th> Customer Mobile </th>
                  <th>Total Amount</th>
                  <th>Commission</th>
                  <th>Commission GST</th>
                  <th>Profit</th>
                  <th>Order Date</th>
                  <th> Completion Date </th>
                </tr>
              </thead>
              <tbody>
              @forelse ($details as $detail)
                <tr>
                @if ($detail->status == 'completed' && $detail->payment_status == 'completed')
                  <td> {{ $detail->order_id }}</td>
                  <td> CRN{{ $detail->invoice_no }}</td>
                  <td> {{$detail->customer_name}} </td>
                  <td> {{$detail->address}}</td>
                  <td>@if (!empty($detail->user_mobile))  {{$detail->user_mobile}} @endif </td>
                  <td>{{ $detail->order_amount }}</td>
                  <td>{{number_format((float)($detail->commission), 2, '.', '')}}</td>
                  <td>{{number_format((float)($detail->commission_gst), 2, '.', '')}}</td>
                  <td> {{ number_format((float)($detail->commission), 2, '.', '') - number_format((float)($detail->commission_gst), 2, '.', '') }}</td>
                  <td>@if(!empty($detail->created_at)) {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail->created_at)->format('d/m/Y g:i A')}} @endif</td>
                  <td>@if(!empty($detail->order_status_date)) {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $detail->order_status_date)->format('d/m/Y g:i A')}} @endif</td>
                  @endif
                </tr>
                @empty
                <tr><td colspan="9" class="text-center">No results found</td></tr>
                @endforelse
              </tbody>
            </table>
            <div>
              {{ $details->appends(request()->except('page'))->links() }}
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
  $(document).ready(function() {
    var form = $('#accounts_filters');
    $('#clear_form').click(function() {
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
