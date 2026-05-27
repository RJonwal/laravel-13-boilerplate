<div class="modal fade show" id="ViewStaff" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-modal="true" >
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">@lang('global.show') @lang('cruds.staff.title_singular')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="mb-2 normal_width_table">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th style="width:210px;"> @lang('cruds.staff.fields.name') </th>
                                    <td> {{ $staff->name ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:210px;"> @lang('cruds.staff.fields.email') </th>
                                    <td> {{ $staff->email ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:210px;"> @lang('cruds.staff.fields.phone') </th>
                                    <td> {{ $staff->phone ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:210px;"> @lang('cruds.staff.fields.role') </th>
                                    <td> {{ $staff->roles[0]->name ?? 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:210px;"> @lang('cruds.staff.fields.status') </th>
                                    <td> {{ $staff->status ? config('constant.status')[$staff->status] : 'N/A' }} </td>
                                </tr>
                                <tr>
                                    <th style="width:210px;"> @lang('cruds.staff.fields.created_at') </th>
                                    <td> {{ $staff->created_at->format(config('constant.date_format.date_time')) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
