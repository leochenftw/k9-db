<?php
namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Award extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '奖项';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = ' 奖项';
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'Award';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'                 =>  'Varchar(128)'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Dog'                   =>  'KSolution\Dog',
        'Competition'           =>  'KSolution\Competition',
        'Certificate'           =>  'SilverStripe\Assets\Image'
    ];

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = [
        'Videos'                =>  'KSolution\Video'
    ];
}
