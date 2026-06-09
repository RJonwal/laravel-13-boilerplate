@extends('Layouts::emails.layouts.admin')

@section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #2D4379; font-weight: 700; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
        {!! trans('emails.user_register_mail_super_admin.body.line1', ['name' => $name], $language) !!}
    </h4>
    <p style="font-weight:400; line-height: 25.5px; margin-bottom: 27px;">{!! trans('emails.user_register_mail_super_admin.body.line2', [], $language) !!}</p>
    
    <ul>
        <li>{!! trans('emails.user_register_mail_super_admin.body.line3', ['username' => $username], $language) !!}</li>
        <li>{!! trans('emails.user_register_mail_super_admin.body.line4', ['userEmail' => $userEmail], $language) !!}</li>
        <li>{!! trans('emails.user_register_mail_super_admin.body.line5', ['role' => $role], $language) !!}</li>
        <li>{!! trans('emails.user_register_mail_super_admin.body.line6', ['phone_number' => $phoneNumber], $language) !!}</li>
    </ul>

    <p style="font-weight:400; line-height: 25.5px;  margin-bottom: 27px;">{!! trans('emails.user_register_mail_super_admin.body.line7', [], $language) !!}</p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; color: #2D4379; line-height: 10.5px; font-weight: 700; font-size: 18px;">
        {{ trans('emails.regards', [], $language) }},<br><br><br>
        {{ trans('emails.project_name', [], $language) }}
    </div>
@endsection
