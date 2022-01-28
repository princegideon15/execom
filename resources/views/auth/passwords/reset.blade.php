@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content-center">
        <div class="col-8 box" style="margin-top:10%;min-height:350px;background-image:url('{{URL::asset('storage/images/bg/login6.jpg')}}');background-size:cover; background-repeat:no-repeat">
                    <div class="row" >
                        <div class="col-7 p-5">
                            <form method="POST" action="{{ url('/reset/password') }}"> 
                            @csrf
                                <div class="form-group"
                                    <label>Email address</label>
                                    <input type="email" 
                                           class="form-control form-control-lg w-100 @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           style="opacity:.9" 
                                           value="{{ old('email') }}"
                                           placeholder="Email">

                                    @error('email')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>New Password</label>
                                    <div class="input-group ">
                                        <input type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror new_password" 
                                           id="password" 
                                           name="password" 
                                           style="opacity:.9"
                                           value="{{ old('password') }}"
                                           placeholder="Password">
                                        <div class="input-group-append">
                                                <span class="input-group-text" style="opacity:.9">
                                                    <a href="javascript:void(0);" id="show_new_password" class="text-dark">
                                                        <span class="fa fa-eye icon"></span>
                                                    </a>
                                                </span>
                                        </div>
                                    </div>
                                
                                    @error('password')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror

                                </div>
                                
                                <div class="form-group">
                                    <label>Repeat Password</label>
                                    <div class="input-group ">
                                        <input type="password"
                                           class="form-control form-control-lg @error('rep_password') is-invalid @enderror rep_password" 
                                           id="rep_password" 
                                           name="rep_password" 
                                           style="opacity:.9"
                                           value="{{ old('rep_password') }}"
                                           placeholder="Password">
                                        <div class="input-group-append">
                                                <span class="input-group-text" style="opacity:.9">
                                                    <a href="javascript:void(0);" id="show_rep_password" class="text-dark">
                                                        <span class="fa fa-eye rep_icon"></span>
                                                    </a>
                                                </span>
                                        </div>
                                    </div>

                                    @error('rep_password')
                                        <strong class="text-danger">{{ $message }}</strong>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-dark w-25">Submit</button>
                            </form>
                        </div>
                        <div class="col-5 p-5" style="background-color:white;opacity:.9;min-height:350px">
                            <hr>
                            <h1>Reset Password</h1>
                            <hr>

                            <div class="alert alert-light ml-0 pl-0 text-dark" role="alert">
                            <strong>Note:</strong> If your account exists in SKMS account, new password will take effect in Execom IS only.
                            </div>

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
