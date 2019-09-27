<?php

namespace Leochenftw\Utils;
use SilverStripe\Core\Config\Config;
use Leochenftw\Debugger;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Captcha\V20190722\CaptchaClient;
use TencentCloud\Captcha\V20190722\Models\DescribeCaptchaResultRequest;
use SilverStripe\Core\Injector\Injector;
use Psr\Log\LoggerInterface;

class TencentCaptcha
{
    public static function validate($ip, $ticket, $randstr)
    {
        $appid      =   Config::inst()->get(__class__, 'appid');
        $secret     =   Config::inst()->get(__class__, 'app_secret');
        $secret_id  =   Config::inst()->get(__class__, 'secret_id');
        $secret_key =   Config::inst()->get(__class__, 'secret_key');

        try {

            $cred = new Credential($secret_id, $secret_key);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("captcha.tencentcloudapi.com");

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new CaptchaClient($cred, "ap-beijing", $clientProfile);

            $req = new DescribeCaptchaResultRequest();

            $params = '{"CaptchaType":9,"Ticket":"' . $ticket . '","UserIp":"' . $ip . '","Randstr":"' . $randstr . '","CaptchaAppId":' . $appid . ',"AppSecretKey":"' . $secret . '"}';
            $req->fromJsonString($params);

            $resp   =   $client->DescribeCaptchaResult($req);
            $resp   =   json_decode($resp->toJsonString());

            if ($resp->CaptchaCode) {
                return true;
            }

            return false;

        } catch (TencentCloudSDKException $e) {
            Injector::inst()->get(LoggerInterface::class)->info($e);
            return false;
        }
    }
}
