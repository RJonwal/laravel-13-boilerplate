<div class="modal fade edit_modal common_modal" id="editUser" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header">
                <h4 class="modal-title" id="editUserLabel">
                    @lang('global.edit') @lang('cruds.user_management.title_singular')
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('global.close')"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body">
                <form id="editUserForm" method="POST" action="{{ route('users.update', $user->uuid ?? '') }}" enctype="multipart/form-data" class="common_form">
                    @csrf
                    @method('PUT')
                    @include('User::partials.form')
                </form>
            </div>
        </div>
    </div>
</div>
