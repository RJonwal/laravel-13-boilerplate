<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

<script src="{{asset('admin-assets/vendor/select2/js/select2.min.js')}}"></script>

<!-- Include intlTelInput JS  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

<script src="{{ asset('admin-assets/vendor/dropify/dropify.min.js') }}"></script>

{!! $dataTable->scripts() !!}

<script>

// $(document).on('shown.bs.modal', '.modal', function () {
//     $('.select2_field').select2({
//         width: '100%',
//         dropdownParent: $('.select2_parent'),
//         dropdownPosition: 'below',
//         selectOnClose: false
//     });
// });

$(document).on('shown.bs.modal', '.modal', function () {
    $(this).find('.select2_field').each(function () {
        let $select = $(this);

        // Destroy if already initialized
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            width: '100%',
            dropdownParent: $select.closest('.select2_parent'),
            dropdownPosition: 'below',
            selectOnClose: false
        });
    });
});


@can('user_show')
    $(document).on("click", ".btnViewUser", function() {
        let url = $(this).data('href');
        pageLoader();

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#ViewUser').modal('show');
                }
                else {
                    toasterAlert('error',response.error);
                }
            },
            error: function(res){
                toasterAlert('error',res.responseJSON.error);
            },
            complete: function(){
                pageLoader('hide');
            }
        });
    });
@endcan

@can('user_edit')
    $(document).on("click", ".btnEditUser", function() {
        pageLoader('show');
        let url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);

                    initDropify();
                    $('#editUser').modal('show');
                    initIntlTelInput("#phone");
                }
                else {
                    toasterAlert('error',response.error);
                }
            },
            error: function(res){
                toasterAlert('error',res.responseJSON.error);
            },
            complete: function(){
                pageLoader('hide');
            }
        });
    });

 $(document).on('submit','#editUserForm', function(e) {
    e.preventDefault();
    pageLoader('show', true);

    $('.validation-error-block').remove();

    var formData = new FormData(this);
    let url = $(this).attr('action');   

    $.ajax({
        type: 'POST',                
        url: url,
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function (response) {
            if(response.success) {
                $('#editUser').modal('hide');
                $('#user-table').DataTable().ajax.reload(null, false);
                toasterAlert('success', response.message);
            } else {
                toasterAlert('error', response.error);
            }
        },
        error: function (response) {
            if(response.status === 400){
                toasterAlert('error',response.responseJSON.error);
            } else if(response.status === 500){
                toasterAlert('error',"{{ trans('messages.error_message') }}");
            } else {
                $.each(response.responseJSON.errors, function (key, item) {
                    let errorLabel = `<span class="validation-error-block">${item[0]}</span>`;
                    let inputElmt = $(`[name='${key}']`);
                    if(inputElmt.closest('.input-group').find('.password-eye').length > 0){
                        inputElmt.closest('.input-group').after(errorLabel);
                    } else {
                        inputElmt.after(errorLabel);
                    }
                });
            }
        },
        complete: function(){
            pageLoader('hide', true);
        }
    });
});

@endcan

@can('user_delete')
    $(document).on("click",".deleteUserBtn", function() {
        let url = $(this).data('href');
        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.onceClickedRecordDeleted') }}",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) {  
                pageLoader('show');
                $.ajax({
                    type: 'DELETE',
                    url: url,
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if(response.success) {
                            $('#user-table').DataTable().ajax.reload(null, false);
                            toasterAlert('success',response.message);
                        }
                        else {
                            toasterAlert('error',response.error);
                        }
                    },
                    error: function(res){
                        toasterAlert('error',res.responseJSON.error);
                    },
                    complete: function(){
                        pageLoader('hide');
                    }
                });
            }
        });
    });
@endcan

@can('user_status')
    $(document).on('click','.user_status_cb', function(){

        console.log('status change');
        let $this = $(this);
        let userId = $this.data('user_id');
        let revertState = !$this.prop('checked');
        let csrf_token = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('global.want_to_change_status') }}",
            icon: "warning",
            showDenyButton: true,
            confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
            denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
        })
        .then(function(result) {
            if (result.isConfirmed) { 
                pageLoader('show'); 
                $.ajax({
                    type: 'POST',
                    url: "{{ route('users.status') }}",
                    dataType: 'json',
                    data: { _token: csrf_token, id: userId },
                    success: function (response) {

                        console.log(response);

                        if(response.status == true) {

                            console.log("table reload");
                            toasterAlert('success',response.message);
                            $('#user-table').DataTable().ajax.reload(null, false);
                        }
                    },
                    error:function (response){
                        $this.prop('checked', revertState);
                        toasterAlert('error',response.error);
                    },
                    complete: function(){
                        pageLoader('hide');
                    }
                });
            } else {
                $this.prop('checked', revertState);
            }
        });
    });
@endcan

@can('user_change_password')
    $(document).on("click", ".btnChangePassword", function() {
        pageLoader();
        let url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#ChangePassword').modal('show');
                }
                else {
                    toasterAlert('error',response.error);
                }
            },
            error: function(res){
                toasterAlert('error',res.responseJSON.error);
            },
            complete: function(){
                pageLoader('hide');
            }
        });
    });

    $(document).on('submit','#ChangePasswordForm', function(e) {
        e.preventDefault();

        let url = $(this).data('href');
        pageLoader('show', true);
        $('.validation-error-block').remove();
        
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                if(response.success) {
                    $('#ChangePassword').modal('hide');
                    $('#user-table').DataTable().ajax.reload(null, false);
                    toasterAlert('success',response.message);
                }
                else {
                    toasterAlert('error', response.error);
                }
            },
            error: function (response) {
                if(response.status === 400){
                    toasterAlert('error',response.responseJSON.error);
                } else if(response.status === 500){
                    toasterAlert('error', "{{trans('messages.error_message')}}")
                } else {
                    $.each(response.responseJSON.errors, function (key, item) {
                        let errorLabel = `<span class="validation-error-block">${item[0]}</span>`;
                        let inputElmt = $(`[name='${key}']`);
                        if (inputElmt.closest('.input-group').find('.password-eye').length > 0) {
                            inputElmt.closest('.input-group').after(errorLabel);
                        } else {
                            inputElmt.after(errorLabel);
                        }
                    });
                }
            },
            complete: function(){
                pageLoader('hide', true);
            }
        });
    }); 
@endcan

$(document).on('click','.user_approval_status', function(){
    let $this = $(this);
    let userId = $this.data('user_id');
    let isApproved = $this.data('status');
    let action = (isApproved == 1) ? "approve" : "reject"; // <- fix here

    let confirmTemplate = "{{ __('global.ban_unban_confirm_text') }}";
    let confirmText = confirmTemplate
        .replace(':action', action)
        .replace(':user_type', 'user');

    let flag = true;
    let csrf_token = $('meta[name="csrf-token"]').attr('content');
    if($this.prop('checked')){
        flag = false;
    }

    Swal.fire({
        title: "{{ trans('global.areYouSure') }}",
        text: confirmText,
        icon: "warning",
        showDenyButton: true, 
        confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",  
        denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
    })
    .then(function(result) {
        if (result.isConfirmed) { 
            pageLoader('show'); 
            $.ajax({
                type: 'POST',
                url: "{{ route('users.isapproved') }}",
                dataType: 'json',
                data: { _token: csrf_token, id: userId , isApproved: isApproved},
                success: function (response) {
                    if(response.status == 'true') {
                        toasterAlert('success',response.message);
                        $('#user-table').DataTable().ajax.reload(null, false);
                    }
                },
                error:function (response){
                    $this.prop('checked', flag);
                    toasterAlert('error',response.error);
                },
                complete: function(xhr){
                    pageLoader('hide');
                }
            });
        } else {
            $this.prop('checked', flag);
        }
    });
});

function initIntlTelInput(selector) {
    var input = document.querySelector(selector);

    if (!input) {
        console.warn("⚠️ Input not found for selector:", selector);
        return null;
    }

    // Initialize intlTelInput
    var iti = window.intlTelInput(input, {
        initialCountry: "ch",                  // Default country: Switzerland
        separateDialCode: true,                // Show country code (+41) separately
        preferredCountries: ["ch", "de", "in", "us"], // Switzerland, Germany, India, US
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
    });

    // On form submit → always set full international number
    $(input.form).on("submit", function () {
        input.value = iti.getNumber(); // e.g., +416377608043
        console.log("📞 Final Phone Value:", input.value);
    });

    // Optional: you can listen for country changes
    // input.addEventListener("countrychange", function () {
    //     input.value = iti.getNumber(); // Update full number on country change
    // });

    return iti; // Return the instance in case you need it later
}


function initDropify() {
    $('.dropify').dropify({
        messages: {
            'default': 'Drag and drop a file or click',
            'replace': 'Drag and drop or click to replace',
            'remove':  'Remove',
            'error':   'Oops, something went wrong.'
        }
    });
}

$(document).ready(function() {
    initDropify(); // initialize on page load
});

// Reinitialize when modal is shown
$('#editUser').on('shown.bs.modal', function() {
    initDropify();
});


$(document).on("click", ".dropify-clear", function() {
    var imageId = $(this).prev('input').attr("id");
    if(imageId == 'image-input-upload') {
        $('#user_image_check').val('true');
    }
});

</script>
