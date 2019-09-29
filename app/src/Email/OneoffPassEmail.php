<?php

namespace App\Web\Email;
use SilverStripe\Control\Email\Email;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;

class OneoffPassEmail extends Email
{
    public function __construct($member) {
        $from       =   Config::inst()->get(Email::class, 'noreply_email');
        $to         =   $member->Email;
        $subject    =   '我爱工作犬平台一次性通行证';

        parent::__construct($from, $to, $subject);

        $this->setHTMLTemplate('Email\\OneoffPass');

        $this->setData([
            'Member'    =>  $member,
            'baseURL'   =>  Director::absoluteURL(Director::baseURL())
        ]);
    }
}
