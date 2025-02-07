<?php

namespace Wuang\Qutility;

use App\Models\GeneralSetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
class Helpmate{
    public static function sysPass(){

        $fileExists = file_exists(__DIR__.'/wuang.json');
        $general = cache()->get('GeneralSetting');
        if (!$general) {
            $general = GeneralSetting::first();
        }
       
        $hasPurchaseCode = cache()->get('purchase_code');
        if (!$hasPurchaseCode) {
            $hasPurchaseCode = env('PURCHASECODE');
            cache()->set('purchase_code',$hasPurchaseCode);
        }

        if (!$fileExists || !$hasPurchaseCode) {
            return false;
        }
        $currentUrl = URL::to('/');
        $param = [
            'url' => env("APP_URL"),
            'code' => $hasPurchaseCode,
            'url_path'=>$currentUrl,
        ];
        // Địa chỉ máy chủ xác thực
        $reqRoute = 'http://127.0.0.1:3030/api/check_listense_key';

         // Gửi yêu cầu POST đến máy chủ và nhận phản hồi
        $response = Http::post($reqRoute, $param); 

        $responseData = $response->json();

        if( empty($responseData) || $responseData['status'] === 'success'){
            return true;
        }
        if($responseData['status'] === 'error'){
         
            return false;
        };

        if ($general->maintenance_mode == 9) {
            return 99;
        }
      
      
        return true; // truy cập vào được 

    }

    public static function appUrl(){
        $current = @$_SERVER['REQUEST_SCHEME'] ?? 'http' . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = substr($current, 0, -9);
        return  $url;
    }
}