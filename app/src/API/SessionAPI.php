<?php

namespace Leochenftw\API;
use Leochenftw\Controllers\APIBaseController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;

class SessionAPI extends APIBaseController
{
    protected static $allowed_request_methods   =   [
        'options'   =>  true,
        'get'       =>  true
    ];

    public function options($request)
    {
        return ':)';
    }

    public function get($request)
    {
        return  [
            'csrf'      =>  SecurityToken::inst()->getSecurityID(),
            'member'    =>  Member::currentUserID()
        ];
    }
}
