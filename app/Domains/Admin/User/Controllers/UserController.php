<?php

namespace App\Domains\Admin\User\Controllers;

use App\Domains\Admin\User\DataTables\UserDataTable;
use App\Domains\Core\User\Models\User;
use App\Domains\Admin\User\Requests\UpdateRequest;
use App\Domains\Core\Role\Models\Role;
use App\Domains\Core\VenueType\Models\VenueType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('User::index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            try {
                $user = User::whereUuid($id)->with('uploads')->firstOrFail();
                
                $viewHTML = view('User::show', compact('user'))->render();
                return response()->json(['success' => true, 'htmlView' => $viewHTML]);
            } catch (\Throwable $th) {
                return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
            }
        }

        return response()->json(['success' => false, 'error' => trans('messages.error_message')], 400);
    }

    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $user = User::whereUuid($id)->with('uploads')->firstOrFail();
            $viewHTML = view('User::edit', compact('user'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $user = User::whereUuid($id)->firstOrFail();
            $input = $request->only('name', 'email', 'phone');

            $user->update($input);

            if($request->has('profile_image')){
                $uploadId = null;
                $actionType = 'save';
                if($profileImageRecord = $user->profileImage){
                    $uploadId = $profileImageRecord->id;
                    $actionType = 'update';
                }
                uploadImage($user, $request->profile_image, 'user/profile-images',"user_profile", 'original', $actionType, $uploadId);
            }  else if($profileImageRecord = $user->profileImage){
                if($request->user_image_check == 'true'){
                    deleteFile($profileImageRecord->id);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
        }
    }

    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $user = User::whereUuid($id)->firstOrFail();
            DB::beginTransaction();
            try {
                if ($user->profile_image_url) {
                    $uploadImageId = $user->profileImage->id;
                    deleteFile($uploadImageId);
                }
                $user->roles()->sync([]);
                $user->delete();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.crud.delete_record'),
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
            }
        }

        return response()->json(['success' => false, 'error' => trans('messages.error_message')], 400);
    }

    public function changeStatus(Request $request)
    {
        abort_if(Gate::denies('user_status'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'exists:users,uuid'],
            ]);

            if (!$validator->passes()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray(),
                    'message' => 'Error Occurred!',
                ], 400);
            }

            DB::beginTransaction();
            try {
                $user = User::where('uuid', $request->id)->first();

                if ($user->status === 'inactive') {
                    $user->update([
                        'status' => 'active',
                    ]);
                } else {
                    $user->update([
                        'status' => 'inactive',
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => trans('cruds.user.title_singular') . ' ' . trans('messages.crud.status_update'),
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                dd($th);
                return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
            }
        }
    }



    public function changePassword(Request $request, $id)
    {
        abort_if(Gate::denies('user_change_password'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $viewHTML = view('User::partials.change-password', compact('id'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
        }
    }

    public function changePasswordSubmit(Request $request, $id)
    {
        abort_if(Gate::denies('user_change_password'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*\p{Lu})(?=.*\p{Ll})(?=.*\d)(?=.*[~`!@#$%^&*()_\-+=:";\'{}\][|\\\\?.,<>\/]).+$/u'
            ],
        ], [
            'password.regex' => trans('validation.password.regex', ["attribute" => strtolower(trans('global.login_password'))])
        ]);

        if ($request->ajax()) {
            $user = User::whereUuid($id)->firstOrFail();
            DB::beginTransaction();
            try {
                $user->update(['password' => Hash::make($request->password)]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => trans('messages.password_updated_successfully'),
                ]);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => $th->getMessage()], 400);
            }
        }

        return response()->json(['success' => false, 'error' => trans('messages.error_message')], 400);
    }

    public function isHostApproved(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id'     => [
                    'required',
                    'exists:users,uuid',
                ],
            ]);
            if (!$validator->passes()) {
                return response()->json(['success' => false, 'errors' => $validator->getMessageBag()->toArray(), 'message' => 'Error Occured!'], 400);
            } else {
                DB::beginTransaction();
                try {
                    $user = User::withoutGlobalScopes()->whereNull('deleted_at')->where('uuid', $request->id)->first();

                    if ($request->isApproved == 1) {
                        $user->update(['approval_status' => $request->isApproved, 'status' => 'active']);

                        /* sendUserNotification(
                            $user->id,
                            'login_approved_title',
                            'login_approved_body',
                            'approved',
                            null,
                            true,
                            ['host' => $user->name],
                        ); */
                    } else if ($request->isApproved == 2) {
                        $user->update(['approval_status' => $request->isApproved, 'status' => 'inactive']);

                        // Notification + Email
                        /* sendUserNotification(
                            $user->id,
                            'login_rejected_title',
                            'login_rejected_body',
                            'rejected',
                            null,
                            true,
                            ['host' => $user->name],
                        ); */
                    }
                    DB::commit();
                    $response = [
                        'status'    => 'true',
                        'message'   => trans('cruds.user.title_singular') . ' ' . trans('messages.crud.status_update'),
                    ];
                    return response()->json($response);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['success' => false,  'error' => trans('messages.error_message')], 400);
                }
            }
        }
    }
}
