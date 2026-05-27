@extends('Layouts::app')
@section('title', trans('global.dashboard'))

@section('custom_css')

@endsection

@section('main-content')

<div class="row mt-3">
    <div class="col-xxl-3 col-sm-6">
        <div class="card widget-flat text-bg-primary">
            <div class="card-body">
                <a class="dashboard-card" href="#">
                    <div class="float-end">
                        <i class="ri-user-line widget-icon"></i>
                    </div>
                    <h6 class="text-uppercase mt-0" >@lang('Users')</h6>
                    <h2 class="my-2">0</h2>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom_js')


@endsection