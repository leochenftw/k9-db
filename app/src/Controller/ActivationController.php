<?php

namespace App\Web\Layout;

use PageController;
use Page;
use Leochenftw\Debugger;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class ActivationController extends PageController
{
    public function getTitle()
    {
        return '帐号激活';
    }

    public function Title()
    {
        return $this->getTitle();
    }

    public function getData()
    {
        $page   =   Page::create();
        $data   =   [];

        if ($page) {
            $data               =   $page->getData();
            $data['title']      =   $this->Title();
            $data['pagetype']   =   'activation';
        }

        $data['code']       =   403;

        if (($code = $this->request->getVar('validation')) && ($id = $this->request->getVar('id'))) {
            if ($member = Member::get()->byID($id)) {
                if ($member->ValidationKey == $code) {
                    $member->ValidationKey  =   null;
                    $member->write();
                    Injector::inst()->get(IdentityStore::class)->logIn($member, true);
                    $data['code']       =   200;
                    $data['content']    =   '<p>帐号激活成功! 即将为您跳转至<a href="/member">用户中心</a></p>';
                } else {
                    $data['code']       =   400;
                    $data['content']    =   '<p>无效激活码! 请<a href="/signup">重新申请</a>.</p>';
                }

                return $data;
            }
            $data['code']       =   404;
            $data['content']    =   '<p>帐号不存在! 请<a href="/signup">重新申请</a>.</p>';
        } else {
            $data['content']    =   '<p>无效请求!</p>';
        }

        return $data;
    }
}
