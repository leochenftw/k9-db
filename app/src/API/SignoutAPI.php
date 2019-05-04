<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use Leochenftw\Debugger;

class SignoutAPI extends RestfulController
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
        if ($member = Member::currentUser()) {
            Injector::inst()->get(IdentityStore::class)->logOut();
            return  [
                'message'   =>  '登出成功!'
            ];
        }

        return $this->httpError(400, '您还没有登录.');
    }
}
