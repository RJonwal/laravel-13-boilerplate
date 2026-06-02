<div class="form_area">
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">@lang('cruds.user.fields.name')<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ isset($user) ? $user->name : '' }}" required>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group">
                <label for="email" class="form-label">@lang('cruds.user.fields.email')<span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ isset($user) ? $user->email : '' }}" required>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="form-group number_code_country">
                <label for="phone" class="form-label">
                    @lang('cruds.user.fields.phone') <span class="text-danger">*</span>
                </label>
                <input type="tel" class="form-control" id="phone" name="phone" value="{{ isset($user) ? $user->phone : '' }}" required>
            </div>
        </div>

        <div class="col-12 col-md-12">
            <div class="form-group">
                <input type="hidden" id="user_image_check" name="user_image_check" />
                <label class="form-label">@lang('cruds.user.fields.profile_image')</label>
                <input
                    name="profile_image"
                    type="file"
                    class="dropify"
                    id="image-input-upload"
                    accept="image/*"
                    data-default-file="{{ $user->profile_image_url ?? '' }}"
                />
            </div>
        </div>
    </div>
</div> 

<div class="card-footer">
    <div class="form_btn p-0">
        <button type="button" class="def-btn" data-bs-dismiss="modal">@lang('global.close')</button>
        <button type="submit" class="submitBtn fill-btn">{{ isset($user) ? __('global.update') : __('global.save') }}</button>
    </div>
</div>