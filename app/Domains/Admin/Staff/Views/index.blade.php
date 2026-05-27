@extends('Layouts::app')
@section('title', trans('cruds.staff.title'))

@section('custom_css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.0/css/responsive.dataTables.min.css">

<link href="{{ asset('admin-assets/vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('main-content')
<div class="content_area">
    <div class="d-flex align-items-center justify-content-between main-title-area">
        <h2 class="main-title">@lang('cruds.staff.title')</h2>
        <div class="top_right_panel d-flex">
            <a href="javascript:void(0);"  class="fill-btn btnAddStaff"><i class="ri-add-line align-bottom me-1"></i> @lang('global.create')</a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="table-responsive common_table">
                    {{$dataTable->table(['class' => 'table mb-0', 'style' => 'width:100%;'])}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom_js')

    @include('Staff::partials.script')

@endsection