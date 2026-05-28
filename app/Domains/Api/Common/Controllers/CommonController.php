<?php
namespace App\Domains\Api\Common\Controllers;

use App\Domains\Core\User\Models\User;
use App\Http\Controllers\APIController;
use Illuminate\Http\Request;
use App\Domains\Core\VenueType\Models\VenueType;
use App\Domains\Core\Setting\Models\Setting;
use App\Domains\Api\Common\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Illuminate\Support\Facades\DB;
class CommonController extends APIController
{
    public function getVenueTypes()
    {
        try {            
            $venueTypes = VenueType::select('uuid', 'name')->get()
            ->map(function($vanueType){
                return [
                    'id' => $vanueType->uuid,
                    'name' => $vanueType->name
                ];
            });
            return $this->apiSuccess($venueTypes, __('api.venue_types_retrieved'));
        } catch (Throwable $th) {
            return $this->apiError(__('api.something_went_wrong'));
        }
    }

    public function getPrivacyAndTerms()
    {
        try {            
            $privacy = Setting::with('pdf')
                ->where('key', 'site_privacy_policy')
                ->first();

            $terms = Setting::with('pdf')
                ->where('key', 'site_term_and_condition')
                ->first();

            $data = [
                'privacy_policy' => $privacy && $privacy->pdf 
                    ? asset('storage/' . $privacy->pdf->file_path) 
                    : null,
                'terms_conditions' => $terms && $terms->pdf
                    ? asset('storage/' . $terms->pdf->file_path)
                    : null,
            ];

            return $this->apiSuccess($data, __('api.privacy_terms_retrieved'));
        } catch (\Throwable $th) {
            return $this->apiError(__('api.something_went_wrong'));
        }
    }

    public function updatePassword(PasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->apiError(__('api.change_password.messages.update_password.validation.invalid_old_password'), 'invalid_old_password');                
            }
            $user->password = bcrypt($request->new_password);
            $user->save();
            DB::commit();
            return $this->apiSuccess([], __('api.change_password.messages.update_password.success_update'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiError(__('api.something_went_wrong'));
        }
    }

    public function getEarlyAccess()
    {
        try {
            $title = Setting::where('key', 'early_access_title')->first();
            $body = Setting::where('key', 'early_access_body')->first();
            $footer = Setting::where('key', 'early_access_footer')->first();

            $data = [
                'title' => $title?->value ?? null,
                'body' => $body?->value ?? null,
                'footer' => $footer?->value ?? null,
            ];

            return $this->apiSuccess($data, __('api.early_access_retrieved'));
        } catch (\Throwable $th) {
            return $this->apiError(__('api.something_went_wrong'));
        }
    }

}