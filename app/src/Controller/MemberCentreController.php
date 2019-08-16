<?php

namespace App\Web\Layout;

use PageController;
use Page;
use Leochenftw\Debugger;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class MemberCentreController extends PageController
{
    public function getTitle()
    {
        return '用户中心';
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
            $data['pagetype']   =   'member-centre';
        }

        if ($action = $this->request->Param('action')) {
            if ($action == 'security') {
                $data['title']  =   '账户安全';
            } elseif ($action == 'breed') {
                $data['title']  =   '繁殖信息';
            } elseif ($action == 'video') {
                $data['title']  =   '视频';
            } elseif ($action == 'photo') {
                $data['title']  =   '照片';
            } else {
                $data['title']  =   $this->getTitle();
            }
        } else {
            $data['title']      =   $this->getTitle();
        }

        return $data;
    }
}
