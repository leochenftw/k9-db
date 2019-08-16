<?php

namespace App\Web\Model;

use SilverStripe\ORM\DataObject;
use App\Web\Layout\DogPage;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DogUsage extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'DogUsage';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'     =>  'Varchar(128)'
    ];

    /**
     * Belongs_many_many relationship
     * @var array
     */
    private static $belongs_many_many = [
        'OriginalUsages'    =>  DogPage::class . '.OriginalUsages',
        'Usages'            =>  DogPage::class . '.Usages'
    ];
}
