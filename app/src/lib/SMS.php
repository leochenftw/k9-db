<?php

namespace Leochenftw\Utils;
use Qcloud\Sms\SmsSingleSender;
use SilverStripe\Core\Config\Config;
use Leochenftw\Debugger;

class SMS
{
    public static function send($mobile_number, $validation_key)
    {
        $appid      =   Config::inst()->get(__class__, 'appid');
        $appkey     =   Config::inst()->get(__class__, 'appkey');
        $templateId =   Config::inst()->get(__class__, 'template_id');
        $smsSign    =   Config::inst()->get(__class__, 'signature');

        $params = [$validation_key, "11"];
        try {
            $ssender = new SmsSingleSender($appid, $appkey);
            $result = $ssender->sendWithParam("86", $mobile_number, $templateId, $params, $smsSign, "", "");
            $rsp = json_decode($result);
            if ($rsp->errmsg == 'OK') {
                return true;
            }
        } catch(\Exception $e) {
            Debugger::inspect($e);
        }

        return false;
    }
}
