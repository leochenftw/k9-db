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
class OneoffPassController extends PageController
{
    public function getTitle()
    {
        return '通行证登录';
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

        if (($code = $this->request->getVar('token')) && ($id = $this->request->getVar('id'))) {
            if ($member = Member::get()->byID($id)) {
                if ($member->OneoffToken == $code) {
                    $member->OneoffToken    =   null;
                    $member->TokenGenTime   =   null;
                    $member->write();
                    Injector::inst()->get(IdentityStore::class)->logIn($member, true);
                    $data['code']       =   200;
                    $data['content']    =   '<p>登录成功! 即将为您跳转至<a href="/member">用户中心</a></p>';
                } else {
                    $data['code']       =   400;
                    $data['content']    =   '<p>通行证无效或已过期! 请<a href="/signin">重新申请通行证</a>.</p>';
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
