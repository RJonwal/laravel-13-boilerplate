<script src="{{asset('admin-assets/vendor/dropify/dropify.min.js')}}"></script>
<script src="{{asset('admin-assets/vendor/tinymce/js/tinymce/tinymce.min.js')}}"></script>
<script>
    $(document).on('submit', '#siteSettingform', function(e){
        e.preventDefault();
        $('.loader-div').show();
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData(this);

        $.ajax({
            type: 'post',
            url: "{{ route('settings.update') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    toasterAlert('success',response.message);
                    window.location.reload();
                }
            },
            error: function (response) {
                console.log(response);
                if(response.status === 400){
                    toasterAlert('error',response.responseJSON.error);
                } else if(response.status === 500){
                    toasterAlert('error', "{{trans('messages.error_message')}}")
                }else {
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

    $(document).on('click', '.submitBtnContent', function(e){
        e.preventDefault();

        pageLoader('show');
        $(".submitBtn").attr('disabled', true);

        $('.validation-error-block').remove();

        var formData = new FormData();

        $('.tinymce-editor').each(function () {
            let id = $(this).attr('id');
            
            let content = tinymce.get(id).getContent({ format: 'text' }).trim();
            formData.append(id, content);
        });

        formData.append('setting_type', "content");
        /* formData.append('_token', "{{ csrf_token() }}");
        $('input[type="file"]').each(function () {
            let input = $(this)[0];
            if (input.files.length > 0) {
                formData.append(input.name, input.files[0]);
            }
        }); */

        $.ajax({
            type: 'post',
            url: "{{ route('settings.update') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
                if(response.success) {
                    toasterAlert('success',response.message);
                    window.location.reload();
                }
            },
            error: function (response) {
                console.log(response);
                if(response.status === 400){
                    toasterAlert('error',response.responseJSON.error);
                } else if(response.status === 500){
                    toasterAlert('error', "{{trans('messages.professional')}}")
                }else {
                    var errorLabelTitle = '';
                    $.each(response.responseJSON.errors, function (key, item) {
                        errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';

                        $(errorLabelTitle).insertAfter("input[name='"+key+"']");
                    });
                }
            },
            complete: function(res){
                $(".submitBtn").attr('disabled', false);
                pageLoader('hide');
            }
        });
    });

    $(document).on('click', '.submitBtnSupport', function(e) {
        e.preventDefault();

        pageLoader('show');
        $(".submitBtnSupport").attr('disabled', true);
        $('.validation-error-block').remove();

        var form = $('#supportSettingform')[0];
        var formData = new FormData(form); 

        $.ajax({
            type: 'POST',
            url: "{{ route('settings.update') }}",
            dataType: 'json',
            contentType: false,
            processData: false,
            data: formData,
            success: function(response) {
                if (response.success) {
                    toasterAlert('success', response.message);
                    window.location.reload();
                }
            },
            error: function(response) {
                console.log(response);
                if (response.status === 400) {
                    toasterAlert('error', response.responseJSON.error);
                } else if (response.status === 500) {
                    toasterAlert('error', "{{ trans('messages.professional') }}");
                } else {
                    $.each(response.responseJSON.errors, function(key, item) {
                        let errorLabel = `<span class="validation-error-block">${item[0]}</span>`;
                        $(`input[name='${key}']`).after(errorLabel);
                    });
                }
            },
            complete: function() {
                $(".submitBtnSupport").attr('disabled', false);
                pageLoader('hide');
            }
        });
    });

    $(document).on('click', '.setting-tab', function(e){
        e.preventDefault();

        var tab_id = $(this).attr('id');

        $('.setting-tab').removeClass('active');
        $(this).addClass('active');

        $('.tab-item').addClass('hide');
        $('.tab-item[data-id="'+tab_id+'"]').removeClass('hide');
    });

    $(document).ready(function () {

        $('.tinymce-editor').each(function () {
            let dir = $(this).data('dir');
            let id = $(this).attr('id');
            let tinySettings = {
                selector: '#'+id, // selects all textareas with this class
                height: 350,
                plugins: [
                    // 'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
            };

            // Check if current textarea has data-dir="rtl"
            if ($(this).data('dir') === 'rtl') {
                tinySettings['directionality'] = 'rtl';
                tinySettings['content_style'] += ' body { direction: rtl; text-align: right; }';
            }

            tinymce.init(tinySettings);
        });
    });

    $('.dropify').dropify();
    $('.dropify-errors-container').remove();
    $('.dropify-wrapper').find('.dropify-clear').hide();
</script>