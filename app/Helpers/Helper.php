<?php

use App\Domains\Core\Setting\Models\Setting;
use App\Domains\Core\Upload\Models\Uploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as Str;
use App\Scopes\ParentReceiptScope;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('printr')) {
    function printr($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die;
    }
}

if (!function_exists('getCommonValidationRuleMsgs')) {
    function getCommonValidationRuleMsgs()
    {
        return [
            'currentpassword.required' => 'The current password is required.',
            'password.required' => 'The new password is required.',
            'password.min' => 'The new password must be at least 8 characters',
            'password.different' => 'The new password and current password must be different.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'The new password confirmation is required.',
            'password_confirmation.min' => 'The new password confirmation must be at least 8 characters',
            'email.required' => 'Please enter email address.',
            'email.email' => 'Email is not valid. Enter email address for example test@gmail.com',
            'email.exists' => "Please Enter Valid Registered Email!",
            'password_confirmation.same' => 'The confirm password and new password must match.',

            'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
            'password.regex' => 'The :attribute must be at least 8 characters and contain at least one uppercase character, one number, and one special character.',
        ];
    }
}

if (!function_exists('generateRandomString')) {
    function generateRandomString($length = 20)
    {
        $randomString = Str::random($length);
        return $randomString;
    }
}

if (!function_exists('getWithDateTimezone')) {
    function getWithDateTimezone($date)
    {
        $newdate = Carbon::parse($date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i:s');
        return $newdate;
    }
}

if (!function_exists('uploadImage')) {
    /**
     * Upload Image.
     *
     * @param array $input
     *
     * @return array $input
     */
    function uploadImage($directory, $file, $folder, $type = "profile", $fileType = "jpg", $actionType = "save", $uploadId = null, $orientation = null)
    {
        $oldFile = null;
        if ($actionType == "save") {
            $upload                       = new Uploads;
        } else {
            $upload                       = Uploads::find($uploadId);
            $oldFile = $upload->file_path;
        }
        $upload->file_path          = $file->store($folder, 'public');
        $upload->extension          = $file->getClientOriginalExtension();
        $upload->original_file_name = $file->getClientOriginalName();
        $upload->type                 = $type;
        $upload->file_type             = $fileType;
        $upload->orientation         = $orientation;
        $response                     = $directory->uploads()->save($upload);
        // delete old file
        if ($oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

        return $upload;
    }
}

if (!function_exists('deleteFile')) {
    /**
     * Destroy Old Image.	 *
     * @param int $id
     */
    function deleteFile($upload_id)
    {
        $upload = Uploads::find($upload_id);
        Storage::disk('public')->delete($upload->file_path);
        $upload->delete();
        return true;
    }
}


if (!function_exists('getSetting')) {
    function getSetting($key)
    {
        $result = null;
        $setting = Setting::where('key', $key)->where('status', 1)->first();

        if (!$setting) {
            return null;
        }

        if ($setting->type == 'image') {
            $result = $setting->image_url;
        } elseif ($setting->type == 'file') {
            $result = $setting->doc_url;
        } elseif ($setting->type == 'json') {
            $result = $setting->value ? json_decode($setting->value, true) : null;
        } else {
            $result = $setting->value;
        }

        return $result;
    }
}