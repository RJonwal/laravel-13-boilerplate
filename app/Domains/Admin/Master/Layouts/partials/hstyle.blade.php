<!-- App css -->
<link href="{{ asset('admin-assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

<!-- Icons css -->
<link href="{{ asset('admin-assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('admin-assets/vendor/fontawesome/css/fontawesome-all.min.css') }}" />


<!-- Theme Config Js -->
<script src="{{ asset('admin-assets/js/config.js') }}"></script>

<!-- Main css -->
<link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/css/style.css') }}">

<style>
    .custom-loading {
        padding: 8px;
        font-size: 14px;
        color: #555;
        text-align: center;
    }

    .custom-loading::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 14px;
        margin-right: 6px;
        border: 2px solid #ccc;
        border-top-color: #007bff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        vertical-align: middle;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .custom-no-results {
        padding: 8px;
        font-size: 14px;
        color: red;
        text-align: center;
    }
</style>