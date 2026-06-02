<div class="modal fade common_modal" id="ViewUser" tabindex="-1" aria-labelledby="viewUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header">
                <h4 class="modal-title" id="viewUserLabel">
                    @lang('global.show') @lang('cruds.user.title_singular')
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('global.close')"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body px-24px pb-32px">
                <div class="card-body">
                    <div class="table-responsive normal_width_table">
                        <table class="table table-striped mb-0">
                            <tbody>
                                <tr>
                                    <th style="width:150px;">@lang('cruds.user.fields.name')</th>
                                    <td>{{ $user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('cruds.user.fields.email')</th>
                                    <td>{{ $user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('cruds.user.fields.phone')</th>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>@lang('cruds.user.fields.profile_image')</th>
                                    <td>
                                        @if($user->profile_image_url)
                                            <img src="{{ $user->profile_image_url }}" class="user-profile-img">
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="modal-footer">
                <button type="button" class="fill-btn m-0" data-bs-dismiss="modal">
                    @lang('global.close')
                </button>
            </div>

        </div>
    </div>
</div>