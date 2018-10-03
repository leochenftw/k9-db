<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Video extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '视频';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = '视频';

    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'Video';
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'                     =>  'Varchar(128)',
        'Description'               =>  'Text',
        'YouKuUrl'                  =>  'Varchar(2048)'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Dog'                       =>  'KSolution\Dog'
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.Title')->setTitle('标题')->setDescription('可以不填');
        $fields->fieldByName('Root.Main.Description')->setTitle('描述')->setDescription('可以不填');
        $fields->fieldByName('Root.Main.YouKuUrl')->setTitle('优酷链接');
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}
