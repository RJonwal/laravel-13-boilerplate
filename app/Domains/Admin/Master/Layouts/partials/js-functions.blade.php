<script>
    function btnloader(type = 'show') {
        if (type === 'show') {
            $("button[type='submit']").attr('disabled', true);
            $('.btn_loader').removeClass('d-none');
        } else {
            $("button[type='submit']").attr('disabled', false);
            $('.btn_loader').addClass('d-none');
        }
    }

    function pageLoader(type = 'show', isForm = false) {
        if (type === 'show') {
            $('.loader-div').show();
            if (isForm) {
                $("button[type='submit']").attr('disabled', true);
            }
        } else {
            $('.loader-div').hide();
            if (isForm) {
                $("button[type='submit']").attr('disabled', false);
            }
        }
    }

    function getReceiptDetails(url, popupTitle = 'Show Receipt', options = {}) {
        pageLoader('show');

        $.ajax({
            type: 'get',
            url: url,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('.popup_render_div').html(response.htmlView);

                    // set modal title dynamically
                    $('#myLargeModalLabel.modal-title').text(popupTitle);

                    // hide buttons for View Page
                    if (options.hideButtons) {
                        $('#ViewReceipt #editReceiptBtn').hide();
                        $('#ViewReceipt .submitBtn').hide();
                        $('#ViewReceipt #cancelEditBtn').hide();
                        $('#ViewReceipt #deleteReceiptBtn').hide();
                        $('#ViewReceipt #printReceiptBtn').hide();
                    }

                    $('#ViewReceipt').modal('show');
                } else {
                    toasterAlert('error', response.error);
                }
            },
            error: function(response) {
                if (response.status === 400) {
                    toasterAlert('error', response.responseJSON.error);
                } else if (response.status === 500) {
                    toasterAlert('error', "{{trans('messages.error_message')}}")
                }
            },
            complete: function(xhr) {
                pageLoader('hide');
            }
        });
    }

    $(document).on('click', '.export_children_btn', function(e) {
        e.preventDefault();

        let exportType = $(this).data('type');
        let keyword = $("input[type='search']").val();

        var url = "{{ url('receipts/') }}/" + $(this).data('parent-id') + "/children/print?export_type=" + exportType + "&keyword=" + keyword;

        exportChildrenData(exportType, url);
    });

    function exportChildrenData(exportType, url) {
        if (exportType == 'print') {
            var win = window.open(url, "_blank", "width=800,height=600");
            win.focus();
        } else if (exportType == 'pdf' || exportType == 'csv') {
            window.open(url, "_blank");
        }
    }
    
    // Open select2 dropdown when focused via TAB
    $(document).on('focus', '.select2-selection--single', function (e) {
        const select2 = $(this).closest('.select2-container').prev('select');

        // Only open if not already open
        if (!select2.select2('isOpen')) {
            select2.select2('open');
        }
    });


    $(document).on('click', '.delete_last_year_record', function () {
        let href = $(this).data('href');

        Swal.fire({
            title: "Are you sure?",
            text: "This will delete the last year receipt and create a combined receipt.",
            icon: "warning",
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: "Delete Only",
            denyButtonText: "Print & Delete",
            cancelButtonText: "Cancel"
        }).then((result) => {

            // DELETE ONLY
            if (result.isConfirmed) {
                fetch(href)
                    .then(res => res.json())
                    .then(res => {
                        if(res.success){
                            location.reload();
                        } else {
                            Swal.fire('Error', res.error, 'error');
                        }
                    });
            }

            // PRINT + DELETE
            if (result.isDenied) {
                fetch(href + '?action=print')
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        const byteChars = atob(data.pdf);
                        const byteNumbers = Array.from(byteChars, c => c.charCodeAt(0));
                        const blob = new Blob([new Uint8Array(byteNumbers)], { type: 'application/pdf' });

                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = data.filename; // ✅ UTF-8 safe
                        a.click();
                        URL.revokeObjectURL(url);

                        location.reload();
                    }
                });
            }

            // CANCEL
            if (result.isDismissed) {
                console.log('Action cancelled');
            }
        });
    });
</script>