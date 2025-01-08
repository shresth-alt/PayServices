@extends('admin.layouts.master')
@section("title") Home Widgets | {{ env('APP_NAME') }} @endsection
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
    <div class="header-elements d-none py-0 mb-3 mb-md-0">
      <div class="breadcrumb">
        <a href="{{ route('widget.add') }}">
          <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" >
            <b><i class="icon-plus2"></i></b>
            Add New Widget
          </button>
        </a>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form action="" method="GET">
    <div class="form-group form-group-feedback form-group-feedback-right search-box">
      <input type="text" class="form-control form-control-lg search-input" placeholder="Search with title" name="squery" value="{{ request('squery') }}">
      <div class="form-control-feedback form-control-feedback-lg">
        <i class="icon-search4"></i>
      </div>
    </div>
    <button type="submit" class="hidden">Search</button>
  </form>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th class="hidden">ID</th>
              <th>Title</th>
              <th>Icon</th>
              <th>Created At</th>
              <th class="text-center" style="width: 10%;">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($home_widgets as $item)
            <tr>
              <td class="hidden"></td>
              <td>{{ $item->title }}</td>
              <td>
                <img src="{{ cloudinary_url($item->public_id.'.'.$item->format, array('width' => 80, 'height' => 80, 'crop' => 'fill')) }}" alt="" style="border-radius: 0.275rem;">
              </td>
              <td>{{ Carbon\Carbon::createFromDate($item->created_at)->format('d/m/Y g:i A') }}</td>
              <td class="text-center">
                <div class="btn-group btn-group-justified">
                  <a href="{{ route('widget.edit', $item->id) }}" class="badge badge-primary badge-icon"> Edit Widget <i class="icon-arrow-right5 ml-1"></i></a>
                  <a href="{{ route('widget.delete', $item->id) }}" class="badge badge-danger badge-icon ml-1 doubleClickDelete" data-popup="tooltip" title="Double click to delete" data-placement="bottom"> <i class="icon-trash"></i> </a>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No results found</td></tr>
            @endforelse
          </tbody>
        </table>
        <div>
          {{ $home_widgets->appends(request()->except('page'))->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

</script>
@endsection