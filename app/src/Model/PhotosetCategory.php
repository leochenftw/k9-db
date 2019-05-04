<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class PhotosetCategory extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'PhotosetCategory';
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title' =>  'Varchar(16)'
    ];

    private static $indexes =   [
        'Title' => [
            'type'      =>  'unique',
            'columns'   =>  ['Title']
        ],
    ];
}
