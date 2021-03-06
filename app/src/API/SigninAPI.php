<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Security\PasswordEncryptor;
use Leochenftw\Debugger;

class SigninAPI extends RestfulController
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
        if ($mobile = $request->postVar('mobile')) {
            if ($member = Member::get()->filter(['Phone' => $mobile])->first()) {
                $encryptor  =   PasswordEncryptor::create_for_algorithm($member->PasswordEncryption);
                if ($encryptor->check($member->Password, $request->postVar('password'), $member->Salt, $member)) {
                    Injector::inst()->get(IdentityStore::class)->logIn($member, $request->postVar('remember'));
                    return  [
                        'message'   =>  '登录成功! 您将进入用户中心!'
                    ];
                }

                return $this->httpError(401, '密码错误. 请重试');
            }

            return $this->httpError(404, '账号不存在!');
        } elseif ($email = $request->postVar('email')) {
            if ($member = Member::get()->filter(['Email' => $email])->first()) {
                $encryptor  =   PasswordEncryptor::create_for_algorithm($member->PasswordEncryption);
                if ($encryptor->check($member->Password, $request->postVar('password'), $member->Salt, $member)) {
                    Injector::inst()->get(IdentityStore::class)->logIn($member, $request->postVar('remember'));
                    return  [
                        'message'   =>  '登录成功! 您将进入用户中心!'
                    ];
                }

                return $this->httpError(401, '密码错误. 请重试');
            }

            return $this->httpError(404, '账号不存在!');
        }

        return $this->httpError(400, '请输入你注册时使用的手机号码.');
    }
}
