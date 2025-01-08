@extends('admin.layouts.master')
@section("title") Services | {{ env('APP_NAME') }} @endsection
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
        <a href="{{ route('service.add') }}">
          <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" >
            <b><i class="icon-plus2"></i></b>
            Add New Service
          </button>
        </a>
      </div>
    </div>
  </div>
</div>
<div class="content">
  <form action="" method="GET">
    <div class="form-group form-group-feedback form-group-feedback-right search-box">
      <input type="text" class="form-control form-control-lg search-input" placeholder="Search with service name" name="squery" value="{{ request('squery') }}">
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
              <th>Name</th>
              <th>Description</th>
              <th>Icon</th>
              <th>Slider image</th>
              <th>Parent Service</th>
              <th>Active</th>
              <th class="text-center" style="width: 10%;"><i class="icon-circle-down2"></i></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($data as $item)
            <tr>
              <td class="hidden"></td>
              <td>{{ $item->name }}</td>
              <td>{{ $item->description }}</td>
              <td>
                <img src="{{ cloudinary_url($item->public_id.'.'.$item->format, array('width' => 80, 'height' => 80, 'crop' => 'fill')) }}" alt="" style="border-radius: 0.275rem;">
              </td>
              <td>
                <img src="{{ cloudinary_url($item->slider_public_id.'.'.$item->slider_image_format, array('width' => 80, 'height' => 80, 'crop' => 'fill')) }}" alt="" style="border-radius: 0.275rem;">
              </td>
              <td>{{ $item->parent_service_name }}</td>
              <td>
              @if($item->is_active)
                <span class="badge badge-flat border-success text-success text-capitalize">Yes</span>
                @else
                <span class="badge badge-flat border-danger text-danger text-capitalize">No</span>
                @endif
              </td>
              <td class="text-center">
                <div class="btn-group btn-group-justified">
                  <a href="{{ route('service.edit', $item->id) }}" class="badge badge-primary badge-icon"> Edit Service <i class="icon-arrow-right5 ml-1"></i></a>
                  <a href="{{ route('service.delete', $item->id) }}" class="badge badge-danger badge-icon ml-1 doubleClickDelete" data-popup="tooltip" title="Double click to delete" data-placement="bottom"> <i class="icon-trash"></i> </a>
                </div>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">No results found</td></tr>
            @endforelse
          </tbody>
        </table>
        <div>
          {{ $data->appends(request()->except('page'))->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
