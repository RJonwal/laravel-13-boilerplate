<div class="modal fade edit_modal common_modal" id="ChangePassword" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">@lang('global.change_password')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="ChangePasswordForm" data-href="{{route('users.change-password', $id)}}">
                    @csrf
                    <div class="card-body">
                        <div class="form_area">
                            <div class="col-12">
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">@lang('cruds.user.fields.confirm_password')</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Enter your password" tabindex="2" required autocomplete="new-password">
                                        <div class="input-group-text toggle-password show-password" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form_btn p-0">
                            <button type="button" class="def-btn" data-bs-dismiss="modal">@lang('global.close')</button>
                            <button type="submit" class="fill-btn submitBtn">@lang('global.save')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>