@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-8 box" style="margin-top:10%;min-height:350px;background-image:url('{{URL::asset('storage/images/bg/login6.jpg')}}');background-size:cover; background-repeat:no-repeat">
                    <div class="row" >
                        <div class="col-7 p-5">
                        @if(isset($activated))
                        <h6 class="mb-3"><span class="fas fa-check-square text-success"></span> <strong>{{ $activated }}</strong></h6>
                        @endif
                            <form method="POST" action="{{ url('/doLogin') }}"> 
                            @csrf
                            
                            @php $checked = (Cookie::get('remember') == 1) ? 'checked' : '' @endphp
                            @php $email = (Cookie::get('email') !== null) ? Cookie::get('email') : old('email')  @endphp
                            @php $pass = (Cookie::get('password') !== null) ? Cookie::get('password') : old('password')  @endphp
                                <div class="form-group mt-3">    
                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                           id="email" 
                                           name="email" 
                                           placeholder="Email address"
                                           style="opacity:.9" 
                                           value="{{ $email }}">

                                    @error('email')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror

                                </div>
                                <div class="form-group">
                                    <div class="input-group">	
                                        <input type="password" class="form-control form-control-lg border border-default @error('password') is-invalid @enderror password"
                                            id="password" 
                                            name="password" 
                                            style="opacity:.8"
                                            placeholder="Password"
                                            value="{{ $pass ??  old('password') }}">
                                        <div class="input-group-append">
                                                <span class="input-group-text" style="opacity:.9">
                                                    <a href="javascript:void(0);" id="show_password" class="text-dark">
                                                        <span class="fa fa-eye icon"></span>
                                                        
                                                        
                                                    </a>
                                                </span>
                                        </div>
                                    </div>
                                
                                    @error('password')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>

                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" {{$checked}}>
                                    <label class="form-check-label">Remember me</label>
                                </div>

                                <button type="submit" class="btn btn-dark btn-lg w-100 font-weight-bold">Log In</button>

                                @if ($message = $errors->first('error'))
                                <div class="text-danger pt-2">
                                    <strong><span class="fas fa-exclamation-circle"></span>  {{ $message }}</strong>
                                </div>
                                @endif

                        

                            </form>
                            <div class="mt-3">
                                <a href="forgot/password">
                                    <small class="font-weight-bold">Forgot password?</small>
                                </a>
                            </div>
                        </div>
                        <div class="col-5 p-5" style="background-color:white;opacity:.9;min-height:350px">
                        
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="{{URL::asset('storage/images/logos/nrcp.png')}}" class="mt-2" height="30%" width="30%">
                                <img src="{{URL::asset('storage/images/logos/skms.png')}}" class=" " height="50%" width="50%">
                                <img src="{{URL::asset('storage/images/logos/execom.png')}}" class=" " height="30%" width="30%">
                            </div>
                            <hr>
                            <h1>ExeCom IS</h1>
                            <h6>Provide the management with quick view of data or information that surround the core functions of NRCP.</h6>
                            <hr>
                     
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
