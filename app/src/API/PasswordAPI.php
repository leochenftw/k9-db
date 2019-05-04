<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use Leochenftw\Debugger;
use KSolution\PhotosetCategory;
use SilverStripe\Security\PasswordEncryptor;
use KSolution\VideoRecord;

class PasswordAPI extends RestfulController
{
    private $member =   null;
    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = [
        'get'       =>  false,
        'post'      =>  '->isAuthenticated'
    ];

    public function isAuthenticated()
    {
        if ($this->member = Member::currentUser()) {
            return true;
        }
        return false;
    }

    public function post($request)
    {
        if ($action = $request->Param('Action')) {
            if ($this->hasMethod($action)) {
                return $this->$action($request);
            }

            return $this->httpError(400, 'method does not exist');
        }

        return $this->httpError(400, 'missing action(s)');
    }

    private function update_pass(&$request)
    {
        if (($cur_pass = $request->postVar('cur_pass')) && ($new_pass = $request->postVar('new_pass'))) {
            $encryptor  =   PasswordEncryptor::create_for_algorithm($this->member->PasswordEncryption);
            if ($encryptor->check($this->member->Password, $cur_pass, $this->member->Salt, $this->member)) {
                $this->member->Password =   $new_pass;
                $this->member->write();
                return true;
            }

            return $this->httpError(401, '当前密码错误!');
        }

        return $this->httpError(400, 'missing variables');
    }

    private function request_reset(&$request)
    {

    }

    private function reset_pass(&$request)
    {

    }
}
