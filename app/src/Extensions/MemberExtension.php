<?php

namespace App\Web\Extensions;
use SilverStripe\ORM\DataExtension;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class MemberExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'QQ'            =>  'Varchar(32)',
        'WeChat'        =>  'Varchar(128)',
        'Phone'         =>  'Varchar(32)',
        'Address'       =>  'Text'
    ];
}
