@extends('Layouts::auth')
@section('title', trans('global.login'))
@section('main-content')
<div class="l_content">
    <div class="container px-0">
        <div class="row items-center justify-center">
            <div class="col-xl-5 col-lg-6">
                <div class="text-center mb-4">
                    <a href="#" title="logo" class="header-logo">
                        {{-- <img src="{{asset('default/logo-dark.png')}}" alt="logo"> --}}
                        <img src="{{ getSetting('site_logo') ? getSetting('site_logo') : asset(config('constant.default.logo')) }}" alt="logo">
                    </a>
                </div>
                <div class="log-register-block">
                    <h2 class="text-center mb-2">Welcome Back !</h2>
                    <p class="text-center">@lang('global.login') to get started with {{config('app.name')}}!</p>
                    <form id="loginForm" class="common_form">
                        <div class="form_area">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="emailaddress" class="form-label">@lang('global.login_email')</label>
                                    <input class="form-control" type="email" name="email" id="email" tabindex="1"   autofocus>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password" class="form-label">@lang('global.login_password')</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" name="password" class="form-control" tabindex="2">
                                        <div class="input-group-text toggle-password show-password" data-password="true">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group d-flex justify-content-between flex-wrap">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                        <label class="form-check-label mb-0" for="checkbox-signin">@lang('global.remember_me')</label>
                                    </div>
                                    <a href="{{ route('forgot.password') }}" class="forgot-text">@lang('global.forgot_password')</a>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="text-start btn-block">
                                    <button class="btn btn-soft-primary w-100" type="submit">
                                        @lang('global.login')
                                        @btnLoader
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('custom_js')

<script>

    // Login Ajax
    $(document).on('submit', '#loginForm', function(e){
        e.preventDefault();
        let formData = new FormData(this);

        $('.validation-error-block').remove();

        btnloader('show');
        $.ajax({
            type: 'post',
            url: "{{route('login.submit')}}",
            data: formData,
            dataType: "json",
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            success: function(response, textStatus, jqXHR){
                window.location.href=response.redirect_url;
            },
            error: function(response, textStatus, jqXHR){
                if(response.status === 400 || response.status === 429){
                    toasterAlert('error',response.responseJSON.message);
                }
                else if(response.status === 403){
                    toasterAlert('error',response.responseJSON.message);
                    setTimeout(function() { window.location.reload(); }, 3000);
                } else {                    
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                        
                        let inputElmt = $(`input[name='${key}']`);                        
                        if (inputElmt.closest('.input-group').find('.password-eye').length > 0) {
                            inputElmt.closest('.input-group').after(errorLabelTitle);
                        } else {
                            inputElmt.after(errorLabelTitle);
                        }
                    });
                }
            },
            complete: function(response, textStatus, jqXHR){
                btnloader('hide');
            }
        });
    });
</script>
@endsection