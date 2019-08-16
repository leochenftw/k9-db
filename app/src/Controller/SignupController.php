<?php

namespace KSolution\Controller;
use PageController;
use GuzzleHttp\Client;
use Leochenftw\Debugger;
use Page;

class SignupController extends PageController
{
    public function getTitle()
    {
        return '注册用户';
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
            $data['pagetype']   =   'signup';
        }

        $data['title']  =   $this->getTitle();

        return $data;
    }
}
