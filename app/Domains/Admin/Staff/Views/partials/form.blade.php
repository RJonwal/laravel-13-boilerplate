<div class="form_area">
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label for="name" class="form-label">@lang('cruds.staff.fields.name') <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ isset($staff) ? $staff->name : '' }}" required>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label for="email" class="form-label">@lang('cruds.staff.fields.email')<span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" value="{{ isset($staff) ? $staff->email : '' }}" required >
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group">
            <label for="phone" class="form-label">@lang('cruds.staff.fields.phone')<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="phone" name="phone" maxlength="10" value="{{ isset($staff) ? $staff->phone : '' }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required >
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="form-group select2-parent-staff-role position-relative">
            <label for="role" class="form-label">@lang('cruds.staff.fields.role')  <span class="text-danger">*</span></label>
            <select name="role" id="role" class="form-control select2_field staff_role_select2 default_select outline_gray">
                <option value="">@lang('global.select') @lang('cruds.staff.fields.role')</option>
                @foreach ($roles as $role)
                    <option value="{{$role->id}}" {{ isset($staff) && $staff->roles->contains($role->id) ? 'selected' : '' }}>{{$role->name}}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if(!isset($staff))
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="password" class="form-label">@lang('global.login_password')</label>
                <div class="input-group input-group-merge">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" tabindex="2" required autocomplete="new-password">
                    <div class="input-group-text toggle-password show-password" data-password="false">
                        <span class="password-eye"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="password_confirmation" class="form-label">@lang('cruds.staff.fields.confirm_password')</label>
                <div class="input-group input-group-merge">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Enter your password" tabindex="2" required autocomplete="new-password">
                    <div class="input-group-text toggle-password show-password" data-password="false">
                        <span class="password-eye"></span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<div class="form_btn pb-0">
    <button type="submit" class="submitBtn fill-btn">@lang('global.save')</button>
    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('global.close')</button> -->
</div>