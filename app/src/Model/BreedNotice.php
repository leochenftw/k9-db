<?php

namespace App\Web\Model;
use KSolution\Dog;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class BreedNotice extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'BreedNotice';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'     =>  'Varchar(128)',
        'Content'   =>  'Text'
    ];

    /**
     * Belongs_to relationship
     * @var array
     */
    private static $belongs_to = [
        'Dog'       =>  Dog::class
    ];
}
