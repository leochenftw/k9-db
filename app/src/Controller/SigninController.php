<?php

namespace KSolution\Controller;
use PageController;
use GuzzleHttp\Client;
use Leochenftw\Debugger;
use Page;

class SigninController extends PageController
{
    public function getTitle()
    {
        return '用户登录';
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
            $data['pagetype']   =   'signin';
        }

        $data['title']  =   $this->getTitle();

        return $data;
    }
}
