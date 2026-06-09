@extends('Layouts::emails.layouts.admin')

@section('email-content')

    <h4 style="font-family: 'Barlow', sans-serif; color: #2D4379; font-weight: 700; font-size: 18px;margin-top: 0;margin-bottom: 20px;">
        {!! trans('emails.user_register_welcome_mail_customer.body.line1', ['user_name' => $user->name], $language) !!}
    </h4>
    
    <p style="line-height: 25.5px; font-weight: 400; margin-bottom: 27px;">{!! trans('emails.user_register_welcome_mail_customer.body.line2', [], $language) !!}</p>

    <p style=" line-height: 25.5px; font-weight: 400; margin-bottom: 27px;">{!! trans('emails.user_register_welcome_mail_customer.body.line3', [], $language) !!}</p>

    <div class="regards" style="font-family: 'Barlow', sans-serif; color: #2D4379; line-height: 10.5px; font-weight: 700; font-size: 18px;">
        {{ trans('emails.regards', [], $language) }},<br><br><br>
        {{ trans('emails.project_name', [], $language) }}
    </div>

@endsection
