<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.0/js/dataTables.responsive.min.js"></script>

{!! $dataTable->scripts() !!}

<script>

$(document).ready(function(e){
    $(document).on('shown.bs.modal', '#AddRole, #editRole', function () {
        const $modal = $(this);
        $modal.find('.select2').each(function () {
            const $select = $(this);
            // Prevent double initialization
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
 
            $select.select2({
                width: '100%',
                dropdownParent: $('.modal-body'),
                dropdownPosition: 'below',
                selectOnClose: false,
            });
        });
    });
});

@can('role_create')
    $(document).on("click", ".btnAddRole", function() {
        pageLoader('show');
        var url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: "{{route('roles.create')}}",
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#AddRole').modal('show');
                    // $('.select2').select2({
                    //     width: '100%',
                    //     dropdownParent: $('#AddRole'),
                    //     selectOnClose: false
                    // });
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

    $(document).on('submit','#AddRoleForm', function(e) {
        e.preventDefault();
        pageLoader('show', true);

        $('.validation-error-block').remove();
        var formData = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: "{{route('roles.store')}}",
            data: formData,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('#AddRole').modal('hide');
                    $('#role-table').DataTable().ajax.reload(null, false);
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
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = `<span class="validation-error-block">${item[0]}</span>`;

                        if(key == 'permissions'){
                            $(`label[for="${key}"]`).closest('.form-group').append(errorLabelTitle);
                        } else {
                            $("input[name='" + key + "']").after(errorLabelTitle);
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

@can('role_show')
    $(document).on("click", ".btnViewRole", function() {
        pageLoader('show');
        var url = $(this).data('href');

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#ViewRole').modal('show');
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

@can('role_edit')
    $(document).on("click", ".btnEditRole", function() {
        pageLoader('show');
        var url = $(this).data('href');
        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('.popup_render_div').html(response.htmlView);
                    $('#editRole').modal('show');

                    $('.permission_box').each(function () {
                        const allBottom = $(this).find('.permission_bottom input[type="checkbox"]');
                        const allChecked = allBottom.length > 0 && allBottom.length === allBottom.filter(':checked').length;

                        $(this).find('.permission_top_check input[type="checkbox"]').prop('checked', allChecked);
                    });
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

    $(document).on('submit','#editRoleForm', function(e) {
        e.preventDefault();
       pageLoader('show', true);

        $('.validation-error-block').remove();
        var formData = $(this).serialize();

        var url = $(this).data('href');

        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function (response) {
                if(response.success) {
                    $('#editRole').modal('hide');
                    $('#role-table').DataTable().ajax.reload(null, false);
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
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</span>';
                        if(key == 'permissions'){
                            $(`label[for="${key}"]`).closest('.form-group').append(errorLabelTitle);
                        } else {
                            $("input[name='" + key + "']").after(errorLabelTitle);
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

@can('role_delete')
    $(document).on("click",".deleteRoleBtn", function() {
        var url = $(this).data('href');
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
                            $('#role-table').DataTable().ajax.reload(null, false);
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

$(document).ready(function () {
    $(document).on('click', '.permission_top h5', function () {
        $(this).closest('.permission_box').find('.permission_bottom').slideToggle();
        $(this).parent('.permission_top').toggleClass('active');
    });

    $(document).on('change', '.permission_top_check input[type="checkbox"]', function () {
        const isChecked = $(this).is(':checked');
        const bottomBox = $(this).closest('.permission_box').find('.permission_bottom');

        if (isChecked) {
            bottomBox.slideDown();
        }
        bottomBox.find('input[type="checkbox"]').prop('checked', isChecked);
    });

    $(document).on('change', '.permission_bottom input[type="checkbox"]', function () {
        const box = $(this).closest('.permission_box');
        const allBottom = box.find('.permission_bottom input[type="checkbox"]');
        const allChecked = allBottom.length === allBottom.filter(':checked').length;

        box.find('.permission_top_check input[type="checkbox"]').prop('checked', allChecked);
    });
});
</script>