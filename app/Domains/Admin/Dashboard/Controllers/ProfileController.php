<?php

namespace App\Domains\Admin\Dashboard\Controllers;

use App\Domains\Core\User\Models\User;
use App\Http\Controllers\Controller;
use App\Rules\MatchOldPassword;
use App\Rules\NoMultipleSpacesRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function showprofile(){
        try {
            $user = auth('web')->user();
            return view('Dashboard::profile', compact('user'));
        } catch (\Throwable $th) {
            abort(500);
        }
    }

    public function updateprofile(Request $request){

        $user = auth('web')->user();
        $updateRecords = [
            'name'  => ['required', 'regex:/^[\p{Devanagari}a-zA-Z\s]+$/u', 'string', 'max:255', new NoMultipleSpacesRule],
            'profile_image'  =>['nullable', 'image', 'max:'.config('constant.profile_max_size'), 'mimes:jpeg,png,jpg'],
        ];

        $request->validate($updateRecords,[
            'name.regex' => trans('validation.only_characters', ["attribute" => strtolower(trans('cruds.profile.fields.name'))]),
            'phone.regex' => trans('validation.starts_with_zero', ['attribute' => strtolower(trans('cruds.partner.fields.phone'))]),
            'profile_image.image' => trans('validation.profile_image.image'),
            'profile_image.image' => trans('validation.profile_image.mimes'),
            'profile_image.max' => trans('validation.max_file', [
                "attribute" => strtolower(trans('cruds.profile.fields.profile_name')), 
                'size' => config('constant.image_max_size_in_mb')
            ]),
        ]);
        
        if($request->ajax()){
            DB::beginTransaction();
            try {
                $user->update($request->all());

                if($request->has('profile_image')){
                    $uploadId = null;
                    $actionType = 'save';
                    if($profileImageRecord = $user->profileImage){
                        $uploadId = $profileImageRecord->id;
                        $actionType = 'update';
                    }
                    uploadImage($user, $request->profile_image, 'user/profile-images',"user_profile", 'original', $actionType, $uploadId);
                }
                DB::commit();

                $user = User::where('id', $user->id)->first();
                
                $data = [
                    'success' => true,
                    'profile_image' => $user->profile_image_url,
                    'auth_name' => $user->name,
                    'message' => trans('messages.crud.update_record'),
                ];
                return response()->json($data, 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
            }
        }
        return response()->json(['success' => false,  'error' => trans('messages.error_message')], 400 );
    }

    public function updateChangePassword(Request $request){
        $user = auth('web')->user();
        $request->validate([
            'current_password'  => ['required', 'string','min:8', new MatchOldPassword],
            'password'   => ['required', 'string', 'min:8', 'different:current_password', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/'],
            'password_confirmation' => ['required','min:8','same:password'],

        ], getCommonValidationRuleMsgs());
        if($request->ajax()){
            DB::beginTransaction();
            try {
                $user->update(['password'=> Hash::make($request->password)]);
                DB::commit();
                $data = [
                    'success' => true,
                    'message' => trans('passwords.reset'),
                ];
                return response()->json($data, 200);
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
            }
        }
        return response()->json(['success' => false,  'error' => trans('messages.error_message')], 400 );
    }

    public function removeProfileImage(Request $request){
        if($request->ajax()){
            DB::beginTransaction();
            try {
                $user = auth('web')->user();
                
                $profileImage = $user->profileImage;
                if($profileImage && isset($profileImage->id)){
                    deleteFile($profileImage->id);

                    DB::commit();
                    $data = [
                        'success' => true,
                        'profile_image' => asset(config('constant.default.user_icon')),
                        'auth_name' => $user->name,
                        'message' => trans('messages.crud.profile.remove_image'),
                    ];
                    return response()->json($data);
                } else {
                    return response()->json(['success' => false, 'error' => trans('messages.crud.profile.remove_image_not_found')], 400 );
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json(['success' => false,  'error' => $th->getmessage()], 400 );
            }
        } 
    
    }
}
