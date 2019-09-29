<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use Leochenftw\Debugger;
use Leochenftw\Utils\TencentCaptcha;

class RecoveryAPI extends RestfulController
{
    private static $allowed_actions   =   [
        'post'      =>  '->isAuthenticated'
    ];

    public function isAuthenticated()
    {
        if (($csrf = $this->request->postVar('csrf'))) {
            return SecurityToken::inst()->getSecurityID() == $csrf;
        }

        return false;
    }

    public function post($request)
    {
        if ($member_id = $request->postVar('member_id')) {
            if ($validation_key = $request->postVar('validation_key')) {
                if ($member =   Member::get()->byID($member_id)) {
                    if ($member->ValidationKey == $validation_key) {
                        $member->Password       =   $request->postVar('password');
                        $member->ValidationKey  =   null;
                        $member->write();

                        Injector::inst()->get(IdentityStore::class)->logIn($member);

                        return  [
                            'message'       =>  '密码已重置! 您将进入用户中心!'
                        ];
                    }

                    return $this->httpError(401, '验证码错误! 请重新获取验证码!');
                }

                return $this->httpError(404, '请再次请求发送验证码');
            }

            return $this->httpError(400, '请提交验证码!');

        } elseif ($mobile = $request->postVar('mobile')) {
            if ($member = Member::get()->filter(['Phone' => $mobile])->first()) {
                if ($member->isActivated()) {
                    $member->populateDefaults();
                    $member->write();
                }
                // $member->send_sms();
                return  [
                    'member_id' =>  $member->ID,
                    'message'   =>  '验证码已发送. 请注意查收手机短信'
                ];
            }

            return $this->httpError(404, '没有该手机号匹配的账号.');
        } elseif (($email = $request->postVar('email')) && ($randstr = $request->postVar('randstr')) && ($ticket = $request->postVar('ticket'))) {
            if (TencentCaptcha::validate($request->getIP(), $ticket, $randstr)) {
                if ($member = Member::get()->filter(['Email' => $email])->first()) {
                    $member->send_oneoff_pass_email();
                }

                return  [
                    'message'   =>  '如果您的帐号存在, 我们将发送一封带有一次性登录通行证的邮件到您的邮箱. 请注意查收并于24小时内使用通行证. 通行证将于颁发的24小时后自动过期.'
                ];
            }

            return $this->httpError(400, '请重新验证!');
        }

        return $this->httpError(400, '请输入手机号码!');
    }
}
