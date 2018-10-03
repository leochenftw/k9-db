<?php
namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Competition extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'Competition';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'                 =>  'Varchar(128)',
        'Date'                  =>  'Date'
    ];
}
