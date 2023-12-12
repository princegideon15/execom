@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
@endcomponent
@endslot

{{-- Body --}}
@isset($slot)
@slot('subcopy')

@component('mail::panel')
<strong style="font-size:20px;">Hi, {{ $name }}</strong>
@endcomponent

<p>Welcome to NRCP ExeCom Information System. Your account has been successfully created. Please use the following details to login.</p>
<br/>
<span id="user_details">
<p>Username: <strong>{{ $email }}</strong>
<br/>
Password: <strong>{{ $password }}</strong></p>
</span>

@component('mail::button', ['url' => 'https://execom.nrcp.dost.gov.ph/'])
Login Here
@endcomponent

@component('mail::subcopy')
<p>This is a system generated message. Please do not reply.
<p>Thank you,
<br/>
<br/>
NRCP Execom IS Team</p>

<div id="footer_img">
    <img src="{{URL::asset('storage/images/logos/nrcp.png')}}" class="mt-2" height="10%" width="10%">
    <img src="{{URL::asset('storage/images/logos/execom.png')}}" class=" " height="11%" width="11%">
</div>
@endcomponent



@endslot
@endisset

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
