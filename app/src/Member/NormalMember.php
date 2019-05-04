<?php

namespace KSolution\Member;
use SilverStripe\Security\Member;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class NormalMember extends Member
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'NormalMember';
}
