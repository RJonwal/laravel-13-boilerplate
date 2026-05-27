<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

<script src="{{asset('admin-assets/vendor/select2/js/select2.min.js')}}"></script>

{!! $dataTable->scripts() !!}

<script>

$(document).on('shown.bs.modal', '.modal', function () {
    $('.select2_field').select2({
        width: '100%',
        dropdownParent: $('.modal'),
        dropdownPosition: 'below',
        selectOnClose: false
    }).on('select2:open', function () {
        // Auto-focus search input on open
        setTimeout(() => {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
});

@can('staff_create')
    $(document).on("click", ".btnAddStaff", function() {
        pageLoader();
        let url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: "{{ route('system-users.create') }}",
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#AddStaff').modal('show');
                }
                else {
                    toasterAlert('error',response.error);
                }
            },
            error: function(res){
                toasterAlert('error',res.responseJSON.error);
            },
            complete: function(xhr){
                pageLoader('hide');
            }
        });
    });

    $(document).on('submit','#AddStaffForm', function(e) {
        e.preventDefault();

        pageLoader('show', true);

        $('.validation-error-block').remove();
        // let formData = $(this).serialize();
        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: "{{route('system-users.store')}}",
            data: formData,
            dataType: 'json',
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            success: function (response) {
                if(response.success) {
                    $('#AddStaff').modal('hide');
                    $('#staff-table').DataTable().ajax.reload(null, false);
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
                    let errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = `<span class="validation-error-block">${item[0]}</span>`;
                        $(`textarea[name='${key}']`).after(errorLabelTitle);

                        let inputElmt = $(`input[name='${key}']`);
                        if(inputElmt.closest('.input-group').find('.password-eye').length > 0){
                            inputElmt.closest('.input-group').after(errorLabelTitle);
                        } else if(key == 'roles'){
                            $(`#roles`).after(errorLabelTitle);
                        } else {
                            inputElmt.after(errorLabelTitle);
                        }
                    });
                }
            },
            complete: function(xhr){
                pageLoader('hide', true);
            }
        });
    }); 
@endcan

@can('staff_show')
    $(document).on("click", ".btnViewStaff", function() {
        let url = $(this).data('href');
        pageLoader();

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#ViewStaff').modal('show');
                }
                else {
                    toasterAlert('error',response.error);
                }
            },
            error: function(res){
                toasterAlert('error',res.responseJSON.error);
            },
            complete: function(xhr){
                pageLoader('hide');
            }
        });
    });
@endcan

@can('staff_edit')
    $(document).on("click", ".btnEditStaff", function() {
        pageLoader('show');
        let url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#editStaff').modal('show');
                }
                else {
                    toasterAlert('error',response.error);
                }
            },
            error: function(res){
                toasterAlert('error',res.responseJSON.error);
            },
            complete: function(xhr){
                pageLoader('hide');
            }
        });
    });

    $(document).on('submit','#editStaffForm', function(e) {
        e.preventDefault();
        pageLoader('show', true);

        $('.validation-error-block').remove();
        // let formData = $(this).serialize();

        var formData = new FormData(this);

        let url = $(this).data('href');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            success: function (response) {
                if(response.success) {
                    $('#editStaff').modal('hide');
                    $('#staff-table').DataTable().ajax.reload(null, false);
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
                    let errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = `<span class="validation-error-block">${item[0]}</span>`;

                        // $(`select[name='${key}']`).after(errorLabelTitle);
                        $("#"+key).siblings('.select2').after(errorLabelTitle);

                        let inputElmt = $(`input[name='${key}']`);
                        if(inputElmt.closest('.input-group').find('.password-eye').length > 0){
                            inputElmt.closest('.input-group').after(errorLabelTitle);
                        } else {
                            inputElmt.after(errorLabelTitle);
                        }
                    });
                }
            },
            complete: function(xhr){
                pageLoader('hide', true);
            }
        });
    }); 
@endcan

@can('staff_delete')
    $(document).on("click",".deleteStaffBtn", function() {
            let url = $(this).data('href');
            Swal.fire({
                title: "{{ trans('global.areYouSure') }}",
                text: "{{ trans('global.onceClickedRecordDeleted') }}",
                icon: "warning",
                showDenyButton: true,  
                //   showCancelButton: true,  
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
                                $('#staff-table').DataTable().ajax.reload(null, false);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.error);
                            }
                        },
                        error: function(res){
                            toasterAlert('error',res.responseJSON.error);
                        },
                        complete: function(xhr){
                            pageLoader('hide');
                        }
                    });
                }
            });
        });
@endcan

@can('staff_status')
    $(document).on('click','.staff_status_cb', function(){
        let $this = $(this);
        let staffId = $this.data('staff_id');
        let flag = true;
        let csrf_token = $('meta[name="csrf-token"]').attr('content');
        if($this.prop('checked')){
            flag = false;
        }
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
                    url: "{{ route('system-users.status') }}",
                    dataType: 'json',
                    data: { _token: csrf_token, id: staffId },
                    success: function (response) {
                        if(response.status == 'true') {
                            toasterAlert('success',response.message);
                            $('#staff-table').DataTable().ajax.reload(null, false);
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
            }
            else {
                $this.prop('checked', flag);
            }
        });
    });
@endcan

@can('staff_change_password')
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
            complete: function(xhr){
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
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting content type
            success: function (response) {
                if(response.success) {
                    $('#ChangePassword').modal('hide');
                    $('#staff-table').DataTable().ajax.reload(null, false);
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
                    let errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = `<span class="validation-error-block">${item[0]}</span>`;
                        $(`textarea[name='${key}']`).after(errorLabelTitle);

                        let inputElmt = $(`input[name='${key}']`);                        
                        if (inputElmt.closest('.input-group').find('.password-eye').length > 0) {
                            inputElmt.closest('.input-group').after(errorLabelTitle);
                        } else {
                            inputElmt.after(errorLabelTitle);
                        }
                    });
                }
            },
            complete: function(xhr){
                pageLoader('hide', true);
            }
        });
    }); 
@endcan

</script>