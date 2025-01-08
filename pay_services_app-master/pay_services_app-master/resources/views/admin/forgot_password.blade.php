@extends("admin.layouts.master")
@section("title")
Forgot Password
@endsection
@section("content")
<form class="registration-form py-5 login-form" action="{{ route('api.otp') }}" data-url="{{route('api.password')}}" method="POST" id="send-otp-form" style="margin: 0 auto 20px auto;">
  <div class="card mb-0">
    <div class="card-body">
      <div class="text-center mb-3">
        <i class="icon-user-tie icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
        <h5 class="mb-0">Forgot Password</h5>
        <span class="d-block text-muted">Enter your credentials below</span>
      </div>
      <div class="form-group form-group-feedback form-group-feedback-left">
        <input type="tel" class="form-control" value="{{old('mobile')}}" placeholder="Mobile" name="mobile" autocomplete="off">
        <div class="form-control-feedback">
          <i class="icon-mobile2 text-muted"></i>
        </div>
      </div>
      <div class="form-group form-group-feedback form-group-feedback-left hidden" id="otp">
        <input type="text" class="form-control" placeholder="Enter OTP" name="otp" autocomplete="off">
        <div class="form-control-feedback">
          <i class="icon-mobile text-muted"></i>
        </div>
      </div>
      <div class="form-group form-group-feedback form-group-feedback-left hidden" id="password">
        <input type="text" class="form-control" placeholder="New Password" name="password" autocomplete="off">
        <div class="form-control-feedback">
        <i class="icon-lock2 text-muted"></i>
        </div>
      </div>
      <div class="form-group form-group-feedback form-group-feedback-left hidden" id="c_password">
        <input type="text" class="form-control" placeholder="Confirm Password" name="confirm_password" autocomplete="off">
        <div class="form-control-feedback">
        <i class="icon-lock2 text-muted"></i>
        </div>
      </div>
      <input class="hidden" type="hidden" name="type" value="forgot_password" >
      <input type="hidden" name="_token" value="">
      @csrf               
      <div class="form-group">
        <button type="button" id="send-otp" class="btn btn-primary btn-block" style="height: 2.8rem; font-size: 1rem;">Send OTP <i class="icon-circle-right2 ml-2"></i></button>
        <button type="button" id="set-password" class="btn btn-primary btn-block hidden" style="height: 2.8rem; font-size: 1rem;">Set Password <i class="icon-circle-right2 ml-2"></i></button>

      </div>
    </div>
  </div>
</form>
@endsection
@section('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    $('#send-otp').on("click", function (e) {
      e.preventDefault();
      var url = $('#send-otp-form').attr('action');
      $.ajax({
        type: "POST",
        dataType: 'JSON',
        url: url,
        data: $('#send-otp-form').serialize(),
        success: function (response) {
        // console.log(response);
          $('#password').removeClass('hidden');
          $('#c_password').removeClass('hidden');
          $('#otp').removeClass('hidden');
          $('#send-otp').addClass('hidden');
          $('#set-password').removeClass('hidden');
        
        },
        error: function (response) {
          alert(response.responseJSON.errors[0]);
          // console.log(response.responseJSON.errors[0]);
        }
      })
    });

    $('#set-password').on("click", function (e) {
      e.preventDefault();
      var url = $('#send-otp-form').data('url');
      $.ajax({
        type: "POST",
        dataType: 'JSON',
        url: url,
        data: $('#send-otp-form').serialize(),
        success: function (response) {
        console.log(response);
        },
        error: function (response) {
          alert(response.responseJSON.errors[0]);
        // console.log(error);
        }
      })
    });

  });
</script>
@endsection