<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield("title")</title>
  <!-- Global stylesheets -->
  <link href="https://fonts.googleapis.com/css?family=Dosis:300,400,500,600,700|Poppins:300,400,500,600,700" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/combined.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/layout.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/backend-custom.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/styles.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/colors.min.css') }}" rel="stylesheet" type="text/css">
  <link href="{{ asset('assets/backend/css/components.min.css') }}" rel="stylesheet" type="text/css">
  <!-- /global stylesheets -->
</head>
<body>
  @if ((!Request::is('/authenticate')))
    @include("admin.includes.header")
  @endif
  <div class="page-content container pt-0">
    <div class="content-wrapper">
      @yield("content")
    </div>
  </div>
  <!-- Core JS files -->
  <script type="text/javascript" src="{{ asset('assets/backend/js/jquery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/backend/js/bootstrap.bundle.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/backend/js/select2.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/backend/js/switchery.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/backend/js/widgets.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/backend/js/uniform.min.js') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/backend/js/jquery.validate.min.js') }}"></script>
  @yield("scripts")
</body>
@include('admin.includes.notification')
<script>
  $('.form-check-input-styled-custom').uniform({
    wrapperClass: 'border-indigo-400 text-indigo-400'
  });
  $(document).ready(function() {
    $('.datepicker').datepicker({
      dateFormat: 'dd/mm/yy'
    });
  });
</script>
</html>