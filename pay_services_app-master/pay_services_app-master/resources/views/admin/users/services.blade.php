@extends('admin.layouts.master')
@section("title") Services | {{ env('APP_NAME') }} @endsection
@section('content')
<br>
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">TOTAL</span>
        <span class="badge badge-primary badge-pill animated flipInX"></span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
  </div>
</div>
<div class="content">
  <form action="" method="GET">
    <div class="form-group form-group-feedback form-group-feedback-right search-box">
      <input type="text" class="form-control form-control-lg search-input" placeholder="Search with service name" name="query">
      <div class="form-control-feedback form-control-feedback-lg">
        <i class="icon-search4"></i>
      </div>
    </div>
    <button type="submit" class="hidden">Search</button>
  </form>
  <div class="card">
    <div class="card-body">
      <div class="row form-group">
        <div class="col-md-12">
          <div class="form-group mb-3 mb-md-2">
            <label class="font-weight-semibold mb-4">SERVICES</label>
            <form action="{{ route('add.services',$id) }}" method="POST">
                <div class="row form-group">
                    <div class="col-md-12">
                        @foreach ($services as $service)
                            <div class="form-check">
                                <label class="form-check-label">
                                <input type="checkbox" class="form-check-input-styled-custom" name="serv[{{ $service->id }}]" value="{{ $service->id }}">
                                {{$service->name}}
                                @csrf
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">SAVE<i class="icon-database-insert ml-1"></i></button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
