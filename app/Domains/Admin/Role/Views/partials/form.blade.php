<div class="form_area">
    <div class="col-12">
        <div class="form-group">
            <label for="name" class="form-label">@lang('cruds.role.fields.name') <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ isset($role) ? $role->name : '' }}" required>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label for="permissions" class="form-label">@lang('cruds.role.fields.permission') <span class="text-danger">*</span></label>
            <div>
                @foreach($groupedPermissions as $module => $permissions)
                    <div class="permission_box">
                        <div class="permission_top {{ $loop->index == 0 ? 'active' : '' }}">
                            <h5 class="text-capitalize">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M6.70538 9.29446C7.09466 8.90519 7.72569 8.90484 8.11538 9.29369L11.2937 12.465C11.684 12.8545 12.316 12.8545 12.7063 12.465L15.8846 9.29369C16.2743 8.90484 16.9053 8.90519 17.2946 9.29446C17.6842 9.68403 17.6842 10.3157 17.2946 10.7052L12.7071 15.2927C12.3166 15.6833 11.6834 15.6833 11.2929 15.2927L6.70538 10.7052C6.31581 10.3157 6.31581 9.68403 6.70538 9.29446Z" fill="#1E293B"/>
                                </svg>
                                {{ trans('cruds.'.$module.'.title_singular') }}
                            </h5>
                            <div class="permission_top_check">
                                <input type="checkbox" class="form-check-input" />
                            </div>
                        </div>
                        <div class="permission_bottom">
                            @foreach($permissions as $permission)
                                <div class="form-check">
                                    <label class="form-check-label" for="perm_{{ $permission['id'] }}">
                                        {{ $permission['title'] }}
                                    </label>
                                    <div>
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="perm_{{ $permission['id'] }}"
                                            name="permissions[]"
                                            value="{{ $permission['id'] }}"
                                            {{ isset($role) && $role->permissions->contains($permission['id']) ? 'checked' : '' }}
                                        >
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="form_btn p-0">
    <button type="button" class="def-btn" data-bs-dismiss="modal">@lang('global.cancel')</button>
    <button type="submit" class="fill-btn submitBtn">@lang('global.save')</button>
</div>