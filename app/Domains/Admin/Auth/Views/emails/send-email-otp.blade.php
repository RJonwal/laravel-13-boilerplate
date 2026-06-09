@extends('Layouts::emails.layouts.admin')

@section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #2D4379; font-weight: 700; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
        {{ trans("emails.profile_verify_email_otp.body.line1", ["user_name" => $user->name ?? ""], $language) }}
    </h4>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 16px;">
        {{ trans("emails.profile_verify_email_otp.body.line2", [], $language) }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 10px;">
        {{ trans("emails.profile_verify_email_otp.body.line3", [], $language) }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;">        
        {{ $otp }}
    </p>
    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;">        
        {{ trans("emails.profile_verify_email_otp.body.line4", ["expire_time" => $expireMinutes], $language) }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;">        
        {{ trans("emails.profile_verify_email_otp.body.line5", [], $language) }}
    </p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; font-weight: 700; font-size: 18px; color: #dfcdce;">
        Regards,
        <span style="font-size: 15px; line-height: 20px; font-weight: 400; font-family: 'Nunito Sans', sans-serif; color: #dfcdce; display: block; margin-top: 5px;">
            {{ config('app.name') }}
        </span>
    </div>

@endsection