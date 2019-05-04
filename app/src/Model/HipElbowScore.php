<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class HipElbowScore extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '髋肘检测结果';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = '髋肘检测结果';

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = [
        'DateMeasured'      =>  '日期',
        'Title'             =>  '检测结果'
    ];
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'HipElbowScore';
    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Dog'               =>  'KSolution\Dog',
        'ScannedCopy'       =>  'SilverStripe\Assets\Image'
    ];

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Hip'               =>  'Varchar(8)',
        'Elbow'             =>  'Varchar(8)',
        'DateMeasured'      =>  'Date'
    ];

    /**
     * Defines a default list of filters for the search context
     * @var array
     */
    private static $searchable_fields = [
        'Hip',
        'Elbow',
        'DateMeasured'
    ];

    public function Title()
    {
        return 'HD' . trim($this->Hip) . '/ED' . trim($this->Elbow);
    }

    public function getTitle()
    {
        return $this->Title();
    }

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->fieldByName('Root.Main.Hip')->setTitle('髋')->setDescription('请勿添加首字母"H". 仅填写数值');
        $fields->fieldByName('Root.Main.Elbow')->setTitle('肘')->setDescription('请勿添加首字母"E". 仅填写数值');
        return $fields;
    }
}
