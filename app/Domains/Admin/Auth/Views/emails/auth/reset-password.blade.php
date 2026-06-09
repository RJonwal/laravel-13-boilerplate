@extends('Layouts::emails.layouts.admin')

@section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #2D4379; font-weight: 700; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
        {{ trans("emails.reset_password_admin_panel.body.line1", ["user_name" => $name ?? ""], $language) }}
    </h4>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 16px;">
        {{ trans("emails.reset_password_admin_panel.body.line2", [], $language) }}
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 10px;">
        {{ trans("emails.reset_password_admin_panel.body.line3", [], $language) }}
    </p>

    <a href="{{ $reset_password_url }}" style="font-family: 'Barlow', sans-serif; color:#fff;font-size: 15px;line-height: 20px;border-radius: 8px;background-color: #00509d; padding: 10px 20px;display: inline-block;text-decoration: none;margin-bottom: 20px;">
        {{ trans("emails.reset_password_admin_panel.body.button", [], $language) }}
    </a>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;"> 
        {{ trans("emails.reset_password_admin_panel.body.line4", [], $language) }} <span style="display: block;color: #00509d;">{{ $reset_password_url }}</span>
    </p>

    <p style="font-size: 15px;line-height: 20px;font-weight: 400;font-family: 'Nunito Sans', sans-serif;color: #2D4379;margin-bottom: 22px;">        
        {{ trans("emails.reset_password_admin_panel.body.line5", [], $language) }}
    </p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; color: #464B70; line-height: 10.5px; font-weight: 700; font-size: 18px;">
        {{ trans('emails.regards', [], $language) }},
        <br><br><br>
        {{ trans('emails.project_name', [], $language) }}
    </div>

@endsection