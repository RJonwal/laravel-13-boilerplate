@extends('Layouts::auth')
@section('title', trans('global.forgot_password_title'))
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
                    <h2 class="text-center mb-2">@lang('global.reset_your_password')</h2>
                    <p class="text-center">@lang('global.reset_password_introduction_line')</p>
                    <form id="reset_password_form" class="common_form">
                        <div class="form_area">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password" class="form-label">@lang('global.login_password')</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" name="password" class="form-control" tabindex="1">
                                        <div class="input-group-text toggle-password show-password" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password-confirm" class="form-label">@lang('global.confirm_password')</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password-confirm" name="password_confirmation" class="form-control" tabindex="2">
                                        <div class="input-group-text toggle-password show-password" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="btn-block">
                                    <button class="btn btn-soft-primary w-100" type="submit">
                                        @lang('global.submit')
                                        @btnLoader
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="text-center mt-2">
                        <a href="{{ route('login') }}" class="text-decoration-underline forgot_text"><i class="ri-arrow-left-line"></i> Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('custom_js')

<script>

// Reset Ajax
$(document).on('submit', '#reset_password_form', function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $('.validation-error-block').remove();
    
    btnloader('show');
    $.ajax({
        type: 'post',
        url: '{{route("reset-new-password")}}',
        data: formData,
        dataType: "json",
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Prevent jQuery from setting content type
        success: function(response, textStatus, jqXHR){
            window.location.href=response.redirect_url;
        },
        error: function(response, textStatus, jqXHR){
            if(response.status === 400){
                toasterAlert('error',response.responseJSON.message);
            } else {                    
                var errorLabelTitle = '';
                $.each(response.responseJSON.errors, function (key, item) {
                    errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                    
                    // $(errorLabelTitle).insertAfter("input[name='"+key+"']");
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