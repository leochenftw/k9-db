<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Cookie;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class PromoClick extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'PromoClick';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'         =>  'Varchar(16)',
        'Cookie'        =>  'Varchar(128)',
        'Contribution'  =>  'Int'
    ];

    /**
     * Add default values to database
     * @var array
     */
    private static $defaults = [
        'Contribution'  =>  1
    ];

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = [
        'Title'         =>  'IP地址',
        'Cookie'        =>  'Cookie',
        'Contribution'  =>  '贡献点击'
    ];

    private static $indexes =   ['Cookie' => true];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Promo' =>  Promotion::class
    ];
}
