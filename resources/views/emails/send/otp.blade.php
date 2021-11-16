@component('mail::message')
# Introduction

Your OTP is {{$otp}}


Thanks,<br>
{{ config('app.name') }}
@endcomponent
