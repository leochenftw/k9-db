<?php

namespace App\Web\Model;

use SilverStripe\SiteConfig\SiteConfig;
use gorriecoe\Link\Models\Link;
use App\Web\Extensions\SortOrderExtension;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class FooterLink extends Link
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'FooterLink';

    /**
     * Defines extension names and parameters to be applied
     * to this object upon construction.
     * @var array
     */
    private static $extensions = [
        SortOrderExtension::class
    ];
    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Siteconfig'    =>  SiteConfig::class
    ];

}
