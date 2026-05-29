<?php
namespace App\Domains\Api\Common\Controllers;

use App\Http\Controllers\APIController;
use App\Domains\Core\Setting\Models\Setting;
class CommonController extends APIController
{

    public function getPrivacyAndTerms()
    {
        try {            
           $data = Setting::whereIn('key', ['privacy_policy', 'term_and_conditionn'])->pluck('value', 'key')->toArray();
            return $this->apiSuccess($data);
        } catch (\Throwable $th) {
            return $this->apiError(__('api.something_went_wrong'));
        }
    }

    public function helpAndSupport()
    {
        try {
            $data = Setting::whereIn('key', ['support_email', 'support_contact'])->pluck('value', 'key')->toArray();
            return $this->apiSuccess($data);
        } catch (\Throwable $th) {
            return $this->apiError(__('api.something_went_wrong'));
        }
    }
}