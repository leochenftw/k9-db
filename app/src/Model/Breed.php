<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Breed extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '种类';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = '种类';
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'KSolution';
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'                     =>  'Varchar(128)'
    ];
}
