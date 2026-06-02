<?php

namespace App\Domains\Admin\Staff\Controllers;

use App\Domains\Admin\Staff\DataTables\StaffDataTable;
use App\Domains\Core\User\Models\User;
use App\Domains\Admin\Staff\Requests\StoreRequest;
use App\Domains\Admin\Staff\Requests\UpdateRequest;
use App\Domains\Core\Role\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(StaffDataTable $dataTable)
    {
        abort_if(Gate::denies('staff_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            return $dataTable->render('Staff::index');
        } catch (\Throwable $e) {
            return abort(500);
        }
    }

    public function create(Request $request)
    {
        abort_if(Gate::denies('staff_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $roles = Role::whereNotIn('id', [config('constant.roles.super_admin')])->select('id', 'name')->orderBy('name','asc')->get();
            $viewHTML = view('Staff::create', compact('roles'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
        }
    }

    public function store(StoreRequest $request)
    {
        abort_if(Gate::denies('staff_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $input = $request->only('name', 'email', 'password', 'phone');
            $input['password'] = Hash::make($input['password']);
            $staff = User::create($input);
            
            $staff->roles()->sync([$request->role]);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.add_record'),
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        abort_if(Gate::denies('staff_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if($request->ajax()) {
            try{
                $staff = User::where('uuid', $id)->first();
             
                $viewHTML = view('Staff::show', compact('staff'))->render();
                return response()->json(array('success' => true, 'htmlView'=>$viewHTML));
            }
            catch (\Throwable $th) {
                return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
            }
        }
        return response()->json(['success' => false,  'error' => trans('messages.error_message')], 400 );
    }

    public function edit(Request $request, $id)
    {
        abort_if(Gate::denies('staff_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $staff = User::where('uuid', $id)->first();
            $roles = Role::whereNotIn('id', [config('constant.roles.super_admin')])->select('id', 'name')->orderBy('name','asc')->get();
            $viewHTML = view('Staff::edit', compact('staff', 'roles'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        abort_if(Gate::denies('staff_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        DB::beginTransaction();
        try {
            $staff = User::where('uuid', $id)->first();

            $input = $request->only('name', 'email', 'phone');
            $staff->update($input);

            $staff->roles()->sync([$request->role]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        abort_if(Gate::denies('staff_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $staff = User::where('uuid', $id)->first();

            DB::beginTransaction();
            try {
                if ($staff->profile_image_url) {
                    $uploadImageId = $staff->profileImage->id;
                    deleteFile($uploadImageId);
                }
                $staff->roles()->sync([]);

                $staff->delete();
                
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.crud.delete_record'),
                ];
                return response()->json($response);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
            }
        }
        return response()->json(['success' => false,  'error' => trans('messages.error_message')], 400 );
    }

    public function changeStatus(Request $request){
        abort_if(Gate::denies('staff_status'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'id'     => [
                    'required',
                    'exists:users,uuid',
                ],
            ]);
            if (!$validator->passes()) {
                return response()->json(['success'=>false,'errors'=>$validator->getMessageBag()->toArray(),'message'=>'Error Occured!'],400);
            }else{
                DB::beginTransaction();
                try{
                    $staff = User::where('uuid', $request->id)->first();
                    if($staff->status == 'inactive'){
                        $status = 'active';
                    } else {
                        $status = 'inactive';
                    }
                    $staff->update(['status' => $status]);

                    DB::commit();
                    $response = [
                        'status'    => 'true',
                        'message'   => trans('cruds.staff.title_singular').' '.trans('messages.crud.status_update'),
                    ];
                    return response()->json($response);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
                }
            }
        }
    }

    public function changePassword(Request $request, $id)
    {
        abort_if(Gate::denies('staff_change_password'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            $viewHTML = view('Staff::partials.change-password', compact('id'))->render();
            return response()->json(['success' => true, 'htmlView' => $viewHTML]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getmessage()], 400 );
        }
    }

    public function changePasswordSubmit(Request $request, $id){
        abort_if(Gate::denies('staff_change_password'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate([
            /* 'password'  => ['required', 'string', 'min:8','confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'], */
            'password'  => ['required', 'min:8','confirmed'], 
        ], [
            'password.regex' => trans('validation.password.regex', ["attribute" => strtolower(trans('global.login_password'))])
        ]);        
        if ($request->ajax()) {
            $staff = User::where('uuid', $id)->first();
            DB::beginTransaction();
            try {
                $input = $request->only('password');

                $input['password'] = Hash::make($request->password);

                $staff->update($input);
                
                DB::commit();
                $response = [
                    'success'    => true,
                    'message'    => trans('messages.password_updated_successfully'),
                ];
                return response()->json($response);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false, 'error' => $th->getMessage()], 400 );
            }
        }
        return response()->json(['success' => false, 'error' => trans('messages.error_message')], 400 );
    }
}
