<!-- Vendor js -->
<script src="{{ asset('admin-assets/js/vendor.min.js') }}"></script>

<!-- App js -->
<script src="{{ asset('admin-assets/js/app.min.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(document).on('click', '[data-bs-toggle="tooltip"]', function () {
        bootstrap.Tooltip.getInstance(this)?.hide();
        $(this).trigger('blur');
    });

    $(document).ready(function(e){
        if (!window.location.href.includes("settings")) {
            localStorage.removeItem('activeTab');
        }

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    });

    $(document).on('shown.bs.modal', '.modal', function () {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    });

    $(document).ajaxSuccess(function(event, xhr, settings, response) {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        // $('.action-col .btn').on('click', function () {
        //     bootstrap.Tooltip.getInstance(this)?.hide();
        // });
    });

    $( document ).ajaxError(function( event, response, settings ) {
        if(response.status == 401){
            window.location.href = "{{ route('login') }}";
        }
    });

    $('table').on('draw.dt', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    })

    $(document).on('click', '.toggle-password', function () {        
        let passwordInput = $(this).prev('input');  
        //console.log(passwordInput);      
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            $(this).removeClass('show-password');
        } else {
            passwordInput.attr('type', 'password');
            $(this).addClass('show-password');
        }
    });

    $(document).on("click",".userLogoutBtn", function() {
        var url = $(this).data('href');
        Swal.fire({
            title: "{{ trans('global.areYouSure') }}",
            text: "{{ trans('messages.logout_confirmation') }}",
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
                    type: 'GET',
                    url: url,
                    dataType: 'json',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function (response) {
                        if(response.success) {
                            window.location.href = response.redirect_url;
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
  
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('toggle-remark')) {
        let td = e.target.closest('td');
        let shortText = td.querySelector('.remark-short');
        let fullText = td.querySelector('.remark-full');

        if (shortText.style.display === "none") {
            shortText.style.display = "block";
            fullText.style.display = "none";
            e.target.textContent = "Show more";
        } else {
            shortText.style.display = "none";
            fullText.style.display = "block";
            e.target.textContent = "Show less";
        }
    }
});


</script>

@include('Layouts::partials.alert')
@include('Layouts::partials.js-functions')