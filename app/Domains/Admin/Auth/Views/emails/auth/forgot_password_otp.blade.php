@extends('Layouts::emails.layouts.admin')

@section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #2D4379; font-weight: 700; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
        {{ trans("emails.reset_password_mobile_app.body.line1", ["user_name" => $user->name ?? ""], $language) }}
    </h4>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 16px;">
        {{ trans("emails.reset_password_mobile_app.body.line2", [], $language) }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 10px;">
        {{ trans("emails.reset_password_mobile_app.body.line3", [], $language) }} {{ $token }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;">        
        {{ trans("emails.reset_password_mobile_app.body.line4", ["expire_time" => $expiretime], $language) }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;">
        
        {{ trans("emails.reset_password_mobile_app.body.line5", [], $language) }}
    </p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; color: #464B70; line-height: 10.5px; font-weight: 700; font-size: 18px;">
        {{ trans('emails.regards', [], $language) }},
        <br><br><br>
        {{ trans('emails.project_name', [], $language) }}
    </div>

@endsection