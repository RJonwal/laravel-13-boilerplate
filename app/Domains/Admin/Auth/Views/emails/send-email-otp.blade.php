@extends('Layouts::emails.layouts.admin')

@section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #dfcdce; font-weight: 700; font-size: 18px; margin-top: 0; margin-bottom: 20px;">
        Dear {{ $user->name ?? 'User' }},
    </h4>

    <p style="font-size: 15px; line-height: 20px; font-weight: 400; font-family: 'Nunito Sans', sans-serif; color: #dfcdce; margin-bottom: 16px;">
        We received a request to reset your password for your account.
    </p>

    <p style="font-size: 15px; line-height: 20px; font-weight: 400; font-family: 'Nunito Sans', sans-serif; color: #dfcdce; margin-bottom: 10px;">
        Please use the following One-Time Password (OTP) to proceed with resetting your password:
    </p>

    <div style="text-align:center; margin: 20px 0;">
        <p style="font-size: 24px; font-weight: 700; color: #c6ad6d; letter-spacing: 3px; margin: 0;">
            {{ $otp }}
        </p>
        <p style="font-size: 14px; color: #dfcdce; margin-top: 10px;">
            This OTP will expire in {{ $expireMinutes }} minutes.
        </p>
    </div>

    <p style="font-size: 15px; line-height: 20px; font-weight: 400; font-family: 'Nunito Sans', sans-serif; color: #dfcdce; margin-bottom: 22px;">
        If you did not request a password reset, you can safely ignore this email.
    </p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; font-weight: 700; font-size: 18px; color: #dfcdce;">
        Regards,
        <span style="font-size: 15px; line-height: 20px; font-weight: 400; font-family: 'Nunito Sans', sans-serif; color: #dfcdce; display: block; margin-top: 5px;">
            {{ config('app.name') }}
        </span>
    </div>

@endsection
