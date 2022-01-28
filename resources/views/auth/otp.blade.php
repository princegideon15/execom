@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-8 box" style="margin-top:10%;min-height:350px;background-image:url('{{URL::asset('storage/images/bg/login6.jpg')}}');background-size:cover; background-repeat:no-repeat">
                    <div class="row" >
                        <div class="col-7 p-5">
                        <!-- @if(isset($activated))
                        <h6 class="mb-3"><span class="fas fa-check-square text-success"></span> <strong>{{ $activated }}</strong></h6>
                        @endif -->
                            <form action="{{ url('/verify_otp') }}" method="POST" > 
                            @csrf
                            @php $mail = $recipient; @endphp
                            <!-- @php $checked = (Cookie::get('remember') == 1) ? 'checked' : '' @endphp
                            @php $email = (Cookie::get('email') !== null) ? Cookie::get('email') : old('email')  @endphp
                            @php $pass = (Cookie::get('password') !== null) ? Cookie::get('password') : old('password')  @endphp -->
                                <h5>One-Time PIN</h5>
                                <p>For protection, please enter the One-Time PIN that has been sent to <strong>{{ $mail }}</strong>.
                                The code will expire in <strong>5 minutes</strong>.</p>
                                <div class="form-group mt-3">    
                                    <input type="text" class="form-control form-control-lg @error('otp') is-invalid @enderror"
                                           id="otp" 
                                           name="otp" 
                                           placeholder="6-digit OTP"
                                           min="0"
                                           maxlength="6"
                                           style="opacity:.9">

                                    @error('otp')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror

                                </div>

                                <button type="submit" class="btn btn-dark">Submit</button>
                                <button type="button" id="resend_login_otp" class="btn btn-outline-dark">Resend OTP via email</button>

                                @if ($message = $errors->first('error'))
                                <div class="text-danger pt-2">
                                    <strong><span class="fas fa-exclamation-circle"></span>  {{ $message }}</strong>
                                </div>
                                @endif

                            </form>
                        </div>
                        <div class="col-5 p-5" style="background-color:white;opacity:.9;min-height:350px">
                            <hr class="mt-5">
                            <h1>ExeCom IS</h1>
                            <h6>Provide the management with quick view of data or information that surround the core functions of NRCP.</h6>
                            <hr>
                            <div class="pt-2">
                                <small class="btn-link font-weight-bold text-underline"><a href="/"><span class="oi oi-caret-left"></span> Back to Login</a></small>
                           </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
