@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content-center">
        <div class="col-8 box" style="margin-top:10%;min-height:150px;background-image:url('{{URL::asset('storage/images/bg/login6.jpg')}}');background-size:cover; background-repeat:no-repeat">
                    <div class="row" >
                        <div class="col-7 p-5">
                        <h2>Success!</h2>
                        <h6 class="mt-3">
                            <span class="fas fa-check-square text-success"></span> Password reset successful!
                        </h6>
                        <h6>
                           <span class="fas fa-envelope text-danger"></span> Please check your inbox to activate your account.
                        </h6>
                        <h6 class="text-primary mt-5">
                            <a href="/"><span class="fas fa-caret-square-left"></span> Back to Login</a>
                        </h6>
                        </div>
                        <div class="col-5 p-5" style="background-color:white;opacity:.9;min-height:350px">
                            <hr class="mt-5">
                            <h1>Execom IS</h1>
                            <h6>Provide the management with quick view of data or information that surround the core functions of NRCP.</h6>
                            <hr>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
