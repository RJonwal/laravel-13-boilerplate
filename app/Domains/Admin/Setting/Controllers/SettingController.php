<?php

namespace App\Domains\Admin\Setting\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Core\Setting\Models\Setting;
use App\Domains\Admin\Setting\Requests\UpdateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class SettingController extends Controller
{ 
    public function index() //get
    {
        abort_if(Gate::denies('setting_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $siteSettings = Setting::where('group','web')->get();
        $supportSettings = Setting::where('group','support')->get();
        $contentSettings = Setting::where('group','content')
        ->where('key','!=','site_about')->get();
        return view('Setting::index', compact('siteSettings','contentSettings','supportSettings'));
    }

    public function updateSiteSetting(UpdateRequest $request, Setting $setting)
    {
        abort_if(Gate::denies('setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $data=$request->all();
        // dd($data);
        try {
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                $setting_value = $value;
                if ($setting) {
                    if ($setting->type === 'image') {
                        if ($value) {
                            $uploadId = $setting->image ? $setting->image->id : null;
                            if($uploadId){
                                uploadImage($setting, $value, 'settings/images/',"setting-image", 'original', 'update', $uploadId);
                            }else{
                                uploadImage($setting, $value, 'settings/images/',"setting-image", 'original', 'save', null);
                            }
                        }
                        $setting_value = null;
                    }
                    else {
                        // Handle other fields
                        $setting->value = $setting_value;
                    }
                    $setting->save();
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => trans('messages.crud.update_record'),
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            // dd($e);
            return response()->json(['success' => false,  'error' => trans('messages.error_message')], 400 );
        }
    }
}
