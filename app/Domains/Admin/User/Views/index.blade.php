@extends('Layouts::app')

@section('title', trans('cruds.user.title'))

{{-- Custom CSS --}}
@section('custom_css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/select2/css/select2.min.css') }}">
    <!-- Include intlTelInput  CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css"/>
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/dropify/dropify.min.css') }}">
@endsection

{{-- Main Content --}}
@section('main-content')
<div class="content_area">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between main-title-area">
        <h2 class="main-title">@lang('cruds.user.title')</h2>
    </div>

    {{-- DataTable Section --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive common_table">
                    {!! $dataTable->table(['class' => 'table mb-0 w-100', 'id' => 'user-table']) !!}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Custom JS --}}
@section('custom_js')
    @include('User::partials.script')
@endsection
