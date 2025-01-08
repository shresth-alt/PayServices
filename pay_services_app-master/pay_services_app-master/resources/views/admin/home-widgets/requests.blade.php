@extends('admin.layouts.master')
@section("title") Widgets Requests | {{ env('APP_NAME') }} @endsection
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
        <span class="font-weight-bold mr-2">TOTAL</span>
        <span class="badge badge-primary badge-pill animated flipInX">{{ $count }}</span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
    <form id="widget_requests_filters" action="{{route('widgets.requests')}}" autocomplete="off" method="GET">
      <input type="hidden" value='0' name="excel_export" />
      <div class="form-group row template">
        <div class="col-lg-3 submit-button-link">
          <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="excel_export"><b><i class="icon-file-excel"></i></b>Export</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="content">

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th class="hidden">ID</th>
              <th>Widget Name</th>
              <th>Customer Name</th>
              <th>Customer Mobile</th>
              <th>Comments</th>
              <th>Alt Mobile</th>
              <th>Village</th>
              <th>Landmark</th>
              <th>City</th>
              <th>State</th>
              <th>Pincode</th>
              <th>Created At</th>
              <th class="text-center" style="width: 10%;">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($widget_requests as $widget_request)
            <tr>
              <td class="hidden"></td>
              <td>{{ $widget_request->title }}</td>
              <td>{{ $widget_request->name }}</td>
              <td>{{ $widget_request->mobile }}</td>
              <td>{{ $widget_request->comments }}</td>
              <td>{{ $widget_request->alt_mobile }}</td>
              <td>{{ $widget_request->village }}</td>
              <td>{{ $widget_request->landmark }}</td>
              <td>{{ $widget_request->city }}</td>
              <td>{{ $widget_request->state }}</td>
              <td>{{ $widget_request->pincode }}</td>
              <td>{{ Carbon\Carbon::createFromDate($widget_request->created_at)->format('d/m/Y g:i A') }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-justified">
                  <a href="{{ route('widgets.request.delete', $widget_request->widget_request_id) }}" class="badge badge-danger badge-icon ml-1 doubleClickDelete" data-popup="tooltip" title="Double click to delete" data-placement="bottom"> <i class="icon-trash"></i> </a>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No results found</td></tr>
            @endforelse
          </tbody>
        </table>
        <div>
          {{ $widget_requests->appends(request()->except('page'))->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

</script>
@endsection
@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    var form = $('#widget_requests_filters');
    $('#excel_export').click(function() {
      form.find("input[name='excel_export']").val(1);
      form.attr('target', '_blank').submit();
      form.removeAttr('target');
      form.find("input[name='excel_export']").val(0);
    });
  });
  
</script>
@endsection