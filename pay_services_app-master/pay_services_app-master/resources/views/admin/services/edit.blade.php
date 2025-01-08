@extends('admin.layouts.master')
@section("title") Update Service - Dashboard
@endsection
@section('content')
<div class="page-header">
  <div class="page-header-content header-elements-md-inline">
    <div class="page-title d-flex">
      <h4><i class="icon-circle-right2 mr-2"></i>
        <span class="font-weight-bold mr-2">Editing</span>
        <span class="badge badge-primary badge-pill animated flipInX">{{ $data->name }}</span>
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
          <form class="jquery-validation-form" id="edit_service" action="{{ route('service.update', $data->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PUT">
            @csrf
            <legend class="font-weight-semibold text-uppercase font-size-sm">
              <i class="icon-address-book mr-2"></i> Service Details
            </legend>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Service Name:</label>
              <div class="col-lg-9">
                <input value="{{ $data->name }}" type="text" class="form-control form-control-lg error" name="name" placeholder="Service Name" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Description:</label>
              <div class="col-lg-9">
                <input value="{{ $data->description }}" type="text" class="form-control form-control-lg error" name="description" placeholder="Service Short Description" required>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Service Charge:</label>
              <div class="col-lg-9">
                <input value="{{ $data->charges }}" type="text" class="form-control form-control-lg" name="charges" placeholder="Service Charges">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Parent Service:</label>
              <div class="col-lg-9">
                <select class="form-control select-search" name="parent_id" id="parent_id">
                  <option value="0">Select Parent Service</option>
                  @foreach ($services as $service)
                  <option value="{{ $service->id }}" {{ $service->id == $data->parent_id ? 'selected' : '' }} class="text-capitalize">{{ $service->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Image Icon:</label>
              <div class="col-lg-9">
                @if (!empty($data->image))
                <img class="slider-preview-image" src="{{ cloudinary_url($data->image->public_id.'.'.$data->image->format, array('width' => 80, 'height' => 80, 'crop' => 'fill')) }}">
                @endif
                <div class="uploader">
                  <div class="uniform-uploader">
                    <input type="file" value="{{ $data->image }}" class="form-control-lg form-control-uniform error" name="image" accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                    <span class="filename mt-2" style="user-select: none;">No file selected</span>
                    <span class="action btn btn-light slider-image-choose" style="user-select: none;">Choose File</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Slider Image:</label>
              <div class="col-lg-9">
                @if (!empty($data->slider_image))
                <img class="slider-preview-slider_img" src="{{ cloudinary_url( $data->slider_image->public_id.'.'.$data->slider_image->format, array('width' => 80, 'height' => 80, 'crop' => 'fill')) }}">
                @endif
                <div class="uploader">
                  <div class="uniform-uploader">
                    <input type="file" class="form-control-lg form-control-uniform" name="slider_image" accept="image/x-png,image/gif,image/jpeg" onchange="readSliderURL(this);">
                    <span class="filename mt-2" style="user-select: none;">No file selected</span>
                    <span class="action btn btn-light slider-image-choose" style="user-select: none;">Choose File</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-lg-3 col-form-label">Status</label>
              <div class="col-lg-9">
                <div class="checkbox checkbox-switchery mt-2">
                  <label>
                    <input value="{{ $data->is_active }}" type="checkbox" class="switchery-primary" {{ $data->is_active ? 'checked' : '' }} name="is_active" data-switchery="true" style="display: none;">
                  </label>
                </div>
              </div>
            </div>  
            <div class="text-right">
              <button type="submit" class="btn btn-primary">
                UPDATE
                <i class="icon-database-insert ml-1"></i>
              </button>
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
        .width(80)
        .height(80)
        .css('borderRadius', '0.275rem');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function readSliderURL(input) {
    if (input.files && input.files[0]) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $('.slider-preview-slider_img')
        .removeClass('hidden')
        .attr('src', e.target.result)
        .width(80)
        .height(80)
        .css('borderRadius', '0.275rem');
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  $(document).ready(function() {
    $('#edit_service').validate({
      rules: {
        name: "required",
        description: "required",
        charges: "required"
        image: {
          required: {
            depends: function(element) {
              return $("#parent_id option:selected").val() == "0"
            }
          }
        }
      },
      messages: {
        name: "Please enter service name",
        description: "Please enter description",
        image: "Please select icon",
        charges: "Please enter service charge"
      }
    });
  });
</script>
@endsection