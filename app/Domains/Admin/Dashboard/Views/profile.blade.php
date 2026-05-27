@extends('Layouts::app')
@section('title', trans('global.profile'))

@section('custom_css')
@endsection

@section('main-content')
<div class="content_area">
    <div class="d-flex align-items-center justify-content-between main-title-area">
        <h2 class="main-title">@lang('global.profile')</h2>
    </div>

    <!-- start page title -->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="profile-image">
                    @if($user->profile_image_url)
                        <img src="{{ $user->profile_image_url }}" alt="user-image"  class="avatar-lg rounded-circle user-profile-img">
                    @else
                        <img src="{{ asset(config('constant.default.user_icon')) }}" alt="user-image"  class="avatar-lg rounded-circle user-profile-img">
                    @endif
                </div>
                <div class="profile_details">
                    <h4 class="ellipsis user-profile-name">{{ ucwords($user->name) }}</h4>
                    <ul>
                        <li>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="13" viewBox="0 0 18 13" fill="none">
                                    <path d="M16.418 0H1.58203C0.711492 0 0 0.708363 0 1.58203V11.0742C0 11.9482 0.711949 12.6562 1.58203 12.6562H16.418C17.2885 12.6562 18 11.9479 18 11.0742V1.58203C18 0.708152 17.2882 0 16.418 0ZM16.175 1.05469L9.40866 7.84315C9.2025 8.04994 8.79761 8.05008 8.59134 7.84315L1.82496 1.05469H16.175ZM1.05469 10.8803V1.77592L5.59213 6.32812L1.05469 10.8803ZM1.82496 11.6016L6.3367 7.07512L7.84438 8.58772C8.46221 9.20756 9.53803 9.20732 10.1557 8.58772L11.6633 7.07516L16.175 11.6016H1.82496ZM16.9453 10.8803L12.4079 6.32812L16.9453 1.77592V10.8803Z" fill="#4f4f4f"></path>
                                </svg>
                            </span> {{ $user->email }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="profile-content">
                    <ul class="nav nav-underline nav-justified gap-0">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#edit-profile" type="button" role="tab" aria-controls="home" aria-selected="true" 
                                href="#edit-profile">
                                <span>Edit Profile</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#change-password" type="button" role="tab" aria-controls="home" aria-selected="true" href="#edit-profile">
                                <span>Change Password</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- profile -->
                        <div id="edit-profile" class="tab-pane active">
                            <div class="user-profile-content">
                                <form id="profile-form" enctype="multipart/form-data" class="common_form">
                                    @csrf
                                    <div class="form_area">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="name">Name<span class="required"> *</span></label>
                                                <input type="text" name="name" value="{{ $user->name }}" id="name" class="form-control" placeholder="Enter your name" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="phone">Profile Image</label>
                                                <input type="file" id="image-input" name="profile_image" class="form-control fileInputBoth" accept="image/*">
                                                <div class="img-prevarea mt-3 {{ $user->profile_image_url ? 'active' : '' }}">
                                                    <img src="{{ $user->profile_image_url ? $user->profile_image_url : asset(config('constant.default.user_icon')) }}" width="100px" height="100px" >
                                                    <div class="remove-profile-image-main">
                                                        @if($user->profile_image_url)
                                                            <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm" title="Remove" id="RemoveProfileImageBtn"><i class="ri-delete-bin-line"></i></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form_btn pb-0">
                                        <button class="fill-btn submitBtn" type="submit">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Change password -->
                        <div id="change-password" class="tab-pane">
                            <div class="user-profile-content">
                                <form id="change-password-form" class="common_form">
                                    @csrf
                                    <div class="form_area">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="current_password">Current Password<span class="required"> *</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter current password" value="{{ old('current_password') }}" tabindex="1" autofocus>
                                                    <div class="input-group-text toggle-password show-password" >
                                                        <span class="password-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="new_password">New Password<span class="required"> *</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password" id="new_password" name="password" class="form-control" placeholder="Enter new password" value="{{ old('password') }}" tabindex="2">
                                                    <div class="input-group-text toggle-password show-password" >
                                                        <span class="password-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label" for="password_confirmation">New Password Confirmation<span class="required"> *</span></label>
                                                <div class="input-group input-group-merge">
                                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Enter new password confirmation" value="{{ old('password_confirmation') }}" tabindex="3">
                                                    <div class="input-group-text toggle-password show-password" >
                                                        <span class="password-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form_btn pb-0">
                                        <button class="fill-btn submitBtn" type="submit">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
</div>
@endsection

@section('custom_js')

<script>
    
    $(document).on('submit', '#profile-form', function(e){
        e.preventDefault();
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);
        $('.loader-div').show();
        $.ajax({
            type: 'post',
            url: "{{ route('update.profile') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                $(".submitBtn").attr('disabled', false);
                if(response.success) {
                    toasterAlert('success',response.message);

                    if(response.profile_image){
                        $('.user-profile-img').attr('src', response.profile_image);
                        $('.remove-profile-image-main').html(' <a href="javascript:void(0);" class="btn btn-outline-danger btn-sm" id="RemoveProfileImageBtn"><i class="ri-delete-bin-line"></i></a>');

                        $('.img-prevarea').addClass('active');
                    }

                    if(response.auth_name){
                        $('.user-profile-name').text(response.auth_name);
                    }


                    $("#image-input").val('');
                }
            },
            error: function (response) {
                // console.log(response);
                $(".submitBtn").attr('disabled', false);
                if(response.status === 400){
                    toasterAlert('error',response.responseJSON.error);
                } else if(response.status === 500){
                    toasterAlert('error', "{{trans('messages.error_message')}}")
                } else {
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';

                        $(errorLabelTitle).insertAfter("input[name='"+key+"']");

                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
                $('.loader-div').hide();
            }
        });
    });

    // Image show in profile page
    $(document).on('change', ".fileInputBoth",function(e){
        var files = e.target.files;
        for (var i = 0; i < files.length; i++) {
            var reader2 = new FileReader();
            reader2.onload = function(e) {
                $('.img-prevarea img').attr('src', e.target.result);
            };
            reader2.readAsDataURL(files[i]);
        }
    });

    $(document).on('submit', '#change-password-form', function(e){
        e.preventDefault();
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{ route('update.change.password') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                $(".submitBtn").attr('disabled', false);
                if(response.success) {
                    $('#change-password-form')[0].reset();
                    toasterAlert('success',response.message);
                }
            },
            error: function (response) {
                $(".submitBtn").attr('disabled', false);
                // console.log(response);
                if(response.status === 400){
                    toasterAlert('error',response.responseJSON.error);
                } else if(response.status === 500){
                    toasterAlert('error', "{{trans('messages.error_message')}}")
                } else {                 
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                        
                        var elementItem = $("input[name='"+key+"']").parent();    
                        $(errorLabelTitle).insertAfter(elementItem);
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
            }
        });
    });

    $(document).on('click', '#RemoveProfileImageBtn', function(e){
        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('messages.crud.profile.onceClickedRecordDeleted') }}",
            icon: "warning",
            showDenyButton: true,  
            //   showCancelButton: true,  
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) {  
                $('.loader-div').show();
                $.ajax({
                    type: 'post',
                    url: "{{ route('remove.profile-image') }}",
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if(response.success) {
                            $('.img-prevarea img').attr('src', response.profile_image);
                            if(response.profile_image){
                                $('.user-profile-img').attr('src', response.profile_image);
                            }
                            toasterAlert('success',response.message);
                            $('.remove-profile-image-main').html('');
                            $('.img-prevarea').removeClass('active');
                        }
                        else {
                            toasterAlert('error',response.error);
                        }
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                    },
                    complete: function(xhr){
                        $('.loader-div').hide();
                    }
                });
            }
        });
    })
</script>

@endsection