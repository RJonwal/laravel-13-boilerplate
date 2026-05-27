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
                    <h2 class="text-center mb-2"> @lang('global.forgot_password_title')?</h2>
                    <p class="text-center">@lang('global.forgot_password_introduction_line')</p>
                    <form id="forgot_password_form" class="common_form">
                        <div class="form_area">
                            @csrf
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="emailaddress" class="form-label">@lang('global.login_email')</label>
                                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" tabindex="1" autofocus>
                                    @error('email')
                                    <span class="invalid-feedback d-block">
                                        {{ $message }}
                                    </span>
                                    @enderror
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
$(document).on('submit', '#forgot_password_form', function(e){
    e.preventDefault();
    let formData = new FormData(this);

    $('.validation-error-block').remove();
    
    btnloader('show');
    $.ajax({
        type: 'post',
        url: '{{route("forgot.password.submit")}}',
        data: formData,
        dataType: "json",
        processData: false, // Prevent jQuery from processing the data
        contentType: false, // Prevent jQuery from setting content type
        success: function(response, textStatus, jqXHR){
            toasterAlert('success',response.message);
        },
        error: function(response, textStatus, jqXHR){
            if(response.status === 400){
                toasterAlert('error',response.responseJSON.message);
            } else {                    
                var errorLabelTitle = '';
                $.each(response.responseJSON.errors, function (key, item) {
                    errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                    
                    $(errorLabelTitle).insertAfter("input[name='"+key+"']");
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
