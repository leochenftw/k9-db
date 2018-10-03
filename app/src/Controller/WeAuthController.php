<?php

namespace KSolution\Controller;
use PageController;
use SilverStripe\Security\SecurityToken;
use GuzzleHttp\Client;
use Leochenftw\Debugger;

class WeAuthController extends PageController
{
    public function index()
    {
        if (($code = $this->request->getVar('code')) && ($csrf = $this->request->getVar('state'))) {
            Debugger::inspect($code, false);
            Debugger::inspect('==================================', false);
            Debugger::inspect($csrf, false);
            Debugger::inspect(SecurityToken::inst()->getSecurityID() == $csrf ? 'Yes' : 'no');
        }
    }
}
