<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Config\Config;
use Leochenftw\Debugger;
use Leochenftw\Utils\TencentCaptcha;

class SignupAPI extends RestfulController
{
    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = [
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

                        if ($username = $request->postVar('username')) {
                            if (Member::get()->filter(['Username' => $username])->count() > 0) {
                                return $this->httpError(403, '用户名已被占用. 请重新输入用户名.');
                            }
                        }

                        $member->Username       =   $username;
                        $member->Password       =   $request->postVar('password');
                        $member->ValidationKey  =   null;
                        $member->write();

                        Injector::inst()->get(IdentityStore::class)->logIn($member);

                        return  [
                            'message'       =>  '注册成功! 您将进入用户中心!'
                        ];
                    }

                    return $this->httpError(401, '验证码错误! 请重新获取验证码!');
                }

                return $this->httpError(404, '请再次请求发送验证码');
            }

            return $this->httpError(400, '请提交验证码!');

        } else {
            if ($mobile = $request->postVar('mobile')) {
                if ($member = Member::get()->filter(['Phone' => $mobile])->first()) {
                    if ($member->isActivated()) {
                        return $this->httpError(403, '该手机号码已被注册.');
                    }

                    $member->send_sms();
                    return  [
                        'member_id' =>  $member->ID,
                        'message'   =>  '验证码已发送. 请注意查收手机短信'
                    ];
                }

                $member         =   Member::create();
                $member->Email  =   $mobile;
                $member->Phone  =   $mobile;
                $member->write();
                $member->send_sms();

                return  [
                    'member_id' =>  $member->ID,
                    'message'   =>  '验证码已发送. 请注意查收手机短信'
                ];
            } elseif (($email = $request->postVar('email')) && ($password = $request->postVar('password')) && ($randstr = $request->postVar('randstr')) && ($ticket = $request->postVar('ticket'))) {
                if (TencentCaptcha::validate($request->getIP(), $ticket, $randstr)) {
                    if ($member = Member::get()->filter(['Email' => $email])->first()) {
                        return $this->httpError(403, '该电子邮箱已被注册');
                    }

                    $member             =   Member::create();
                    $member->Email      =   $email;
                    $member->Password   =   $password;
                    $member->write();
                    $member->send_confirmation_email();

                    Injector::inst()->get(IdentityStore::class)->logIn($member, true);

                    return  [
                        'member_id' =>  $member->ID,
                        'message'   =>  '账号激活邮件已发送, 请注意查收.'
                    ];
                }

                return $this->httpError(400, '请重新验证!');
            }
        }

        return $this->httpError(500, 'not sure');
    }
}
