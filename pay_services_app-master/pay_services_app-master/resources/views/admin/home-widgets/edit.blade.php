@extends('admin.layouts.master')
@section("title") Add Widget - Dashboard
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Editing</span>
        <span class="badge badge-primary badge-pill animated flipInX">{{ $data->title }}</span>
      </h4>
      <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
    </div>
  </div>
</div>
<div class="content">
  <div class="row">
    <div class="col-md-7">
      <div class="card">
        <div class="card-body">
          <form id="widget_form"  class="jquery-validation-form" action="{{ route('widget.update', $data->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PUT">
            @csrf
            <legend class="font-weight-semibold text-uppercase font-size-sm">
              <i class="icon-address-book mr-2"></i> Enter Details
            </legend>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Widget Title:</label>
              <div class="col-lg-9">
                <input value="{{ $data->title }}" type="text" class="form-control form-control-lg" name="title" placeholder="Enter widget title" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Widget Icon:</label>
              <div class="col-lg-9">
                @if (!empty($data->icon))
                <img class="slider-preview-image" src="{{ cloudinary_url($data->Image->public_id.'.'.$data->Image->format, array('width' => 80, 'height' => 80, 'crop' => 'fill')) }}">
                @endif
                <div class="uploader">
                  <div class="uniform-uploader">
                    <input type="file" class="form-control-lg form-control-uniform" name="image" accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                    <span class="filename" style="user-select: none;">No file selected</span>
                    <span class="action btn btn-light" style="user-select: none;">Choose File</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-right">
              <button type="submit" class="btn btn-primary">Update Widget<i class="icon-database-insert ml-1"></i></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  function readURL(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.slider-preview-image')
        .removeClass('hidden')
        .attr('src', e.target.result)
        .width(120)
        .height(120)
        .css('borderRadius', '0.275rem');
        $('.filename').html(input.files[0].name);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  $(document).ready(function() {
    $('#widget_form').validate({
      rules: {
        title: "required"
      },
      messages: {
        title: "Please enter widget name"
      }
    });
  });
</script>
@endsection