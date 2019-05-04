<?php

namespace Leochenftw\API;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use Leochenftw\Debugger;
use Leochenftw\Restful\RestfulController;


class MemberAPI extends RestfulController
{
    private $member =   null;

    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = [
        'get'       =>  true,
        'post'      =>  '->isAuthenticated'
    ];

    public function isAuthenticated()
    {
        if ($this->member = Member::currentUser()) {
            if (($csrf = $this->request->postVar('csrf'))) {
                return SecurityToken::inst()->getSecurityID() == $csrf;
            }
        }

        return false;
    }

    public function post($request)
    {
        if ($portrait_data = $request->postVar('portrait_data')) {
            $portrait_data  =   json_decode($request->postVar('portrait_data'));

            if ($portrait = $request->postVar('portrait')) {
                $this->member->create_portrait($portrait['tmp_name'], $portrait['name'], $portrait_data);
                return true;
            }

            if (!empty($portrait_data->id)) {
                $this->member->update_portrait($portrait_data, $portrait_data->id);
                return true;
            }

            return false;
        }

        if (!empty($request->postVar('delete_kennel'))) {
            if ($this->member->KennelCert()->exists()) {
                $this->member->KennelCert()->delete();
            }
        } elseif ($k_cert = $request->postVar('kennel_cert')) {
            if ($this->member->KennelCert()->exists()) {
                $this->member->KennelCert()->delete();
            }
            $this->member->KennelCertID =   $this->member->create_file($k_cert['tmp_name'], $k_cert['name']);
            $this->member->write();
            return true;
        }

        if ($resume = $request->postVar('resume')) {
            if ($this->member->Resume()->exists()) {
                $this->member->Resume()->delete();
            }
            $this->member->ResumeID =   $this->member->create_file($resume['tmp_name'], $resume['name']);
            $this->member->write();
            return true;
        }

        if ($cert = $request->postVar('cert')) {
            if ($this->member->TrainerCert()->exists()) {
                $this->member->TrainerCert()->delete();
            }
            $this->member->TrainerCertID    =   $this->member->create_file($cert['tmp_name'], $cert['name']);
            $this->member->write();
            return true;
        }

        $this->member->Email        =   $request->postVar('email') == 'null' ?
                                        null :
                                        $request->postVar('email');
        $this->member->Username     =   $request->postVar('nickname') == 'null' ?
                                        null :
                                        $request->postVar('nickname');
        $this->member->WeChat       =   $request->postVar('wechat') == 'null' ?
                                        null :
                                        $request->postVar('wechat');
        $this->member->Phone        =   $request->postVar('mobile') == 'null' ?
                                        null :
                                        $request->postVar('mobile');
        $this->member->Identity     =   $request->postVar('identity') == 'null' ?
                                        null :
                                        $request->postVar('identity');
        $this->member->Province     =   $request->postVar('province') == 'null' ?
                                        null :
                                        $request->postVar('province');
        $this->member->City         =   $request->postVar('city') == 'null' ?
                                        null :
                                        $request->postVar('city');
        $this->member->Suburb       =   $request->postVar('suburb') == 'null' ?
                                        null :
                                        $request->postVar('suburb');
        $this->member->Company      =   $request->postVar('company') == 'null' ?
                                        null :
                                        $request->postVar('company');
        $this->member->JobTitle     =   $request->postVar('jobtitle') == 'null' ?
                                        null :
                                        $request->postVar('jobtitle');
        $this->member->Occupation   =   $request->postVar('occupation') == 'null' ?
                                        null :
                                        $request->postVar('occupation');
        $this->member->YearsExp     =   $request->postVar('years') == 'null' ?
                                        null :
                                        $request->postVar('years');

        $this->member->write();

        return $this->member->getData();
    }

    public function get($request)
    {
        if ($member =   Member::currentUser()) {
            return $member->getData();
        }

        return $this->httpError(403, '未登录');
    }
}
