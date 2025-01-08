@extends("admin.layouts.master")
@section("title")
Login
@endsection
@section("content")
<form class="registration-form py-5 login-form" 
action="{{ route('authenticate') }}" method="POST" id="loginForm" 
style="margin: 0 auto 20px auto;">
  <div class="card mb-0">
    <div class="card-body">
      <div class="text-center mb-3">
        <i class="icon-user-tie icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
        <h5 class="mb-0">Login to Dashboard</h5>
        <span class="d-block text-muted">Enter your credentials below</span>
      </div>
      <div class="form-group form-group-feedback form-group-feedback-left">
        <input type="text" class="form-control" placeholder="Email" name="email" autocomplete="off">
        <div class="form-control-feedback">
          <i class="icon-user text-muted"></i>
        </div>
      </div>
      <div class="form-group form-group-feedback form-group-feedback-left">
        <input type="password" class="form-control" placeholder="Password" name="password" autocomplete="off">
        <div class="form-control-feedback">
          <i class="icon-lock2 text-muted"></i>
        </div>
      </div>
      <input type="hidden" name="_token" value="">
      @csrf               
      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-block" style="height: 2.8rem; font-size: 1rem;">Log in <i class="icon-circle-right2 ml-2"></i></button>
      </div>
      <div class="form-group text-center ">
      <a href="{{route('forgot.password')}}">Forgot Password? </a>
      </div>
    </div>
  </div>
</form>
@endsection