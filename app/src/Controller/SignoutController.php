<?php

namespace KSolution\Controller;
use PageController;
use GuzzleHttp\Client;
use Leochenftw\Debugger;
use Page;
use SilverStripe\Core\Config\Config;
use Leochenftw\Utils\TencentCaptcha;

class SignoutController extends PageController
{
    public function getTitle()
    {
        return '退出登录';
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
            $data['pagetype']   =   'signout';
        }

        return $data;
    }
}
