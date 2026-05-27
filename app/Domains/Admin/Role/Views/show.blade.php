<div class="modal fade show" id="ViewRole" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-modal="true" >
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">{{ $role->name ?? 'N/A' }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-24px pb-32px">
                <div class="table-responsive common_table">
                    <table>
                        <thead>
                            <tr>
                                <th class="w-90px">S. No.</th>
                                <th>Name</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($groupedPermissions->count()>0)
                                @foreach($groupedPermissions as $module => $permissions)
                                    <tr>
                                        <td>{{ $loop->iteration }}.</td>
                                        <td>{{ trans('cruds.'.$module.'.title_singular') }}</td>
                                        <td>
                                            <div class="per_data_field">
                                                @foreach($permissions as $permission)
                                                    <span class="per_name">{{ $permission->{'title' } }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">N/A</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- <div class="card-body">
                    <div class="mb-2 normal_width_table">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width:150px;"> @lang('cruds.role.fields.name')</th>
                                    <td> {{ $role->name ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:150px;"> @lang('cruds.role.fields.permission')</th>
                                    <td>
                                        <div class="column_3blog">
                                            @if($groupedPermissions->count())
                                                @foreach($groupedPermissions as $module => $permissions)
                                                    <div>
                                                        <strong>{{ trans('cruds.'.$module.'.title_singular') }}</strong>
                                                        <ul style="margin: 0; padding-left: 18px;">
                                                            @foreach($permissions as $permission)
                                                                <li>{{ $permission->{'title' } }} </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endforeach
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th> @lang('cruds.role.fields.created_at')</th>
                                    <td> {{ $role->created_at->translatedFormat(config('constant.date_format.date_time')) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('global.close')</button>
            </div>
        </div>
    </div>
</div>
