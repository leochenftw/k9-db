<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\Member;
use Leochenftw\Debugger;
use KSolution\Dog;

class DogLookupAPI extends RestfulController
{
    private static $allowed_actions = [
        'post'       =>  true
    ];

    public function post($request)
    {
        if ($member = Member::currentUser()) {
            if ($serial = $request->postVar('serial')) {
                $query  =   Dog::get()->filter(['ChipsSerial' => $serial, 'Sex' => $request->postVar('sex')]);
                if ($exclude = $request->postVar('exclude')) {
                    $query  =   $query->exclude(['ID' => $exclude]);
                }
                if ($dog = $query->first()) {
                    return $dog->getData();
                }

                return $this->httpError(404, '查无此犬');
            }
        }

        return $this->httpError(403, '请先登录!');
    }
}
