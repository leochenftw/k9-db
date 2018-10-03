<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class TradingRecord extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '交易记录';
    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = '交易记录';
    /**
     * Database fields
     * @var array
     */
    private static $db              =   [
                                            'DateHappened'      =>  'Date',
                                            'SoldPrice'         =>  'Currency'
                                        ];
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name      =   'TradingRecord';
    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one         =   [
                                            'Dog'               =>  'KSolution\Dog',
                                            'From'              =>  'SilverStripe\Security\Member',
                                            'To'                =>  'SilverStripe\Security\Member'
                                        ];

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields  =   [
                                            'DateHappened'      =>  '交易日期',
                                            'From.Title'        =>  '卖家',
                                            'To.Title'          =>  '买家'
                                        ];

    /**
     * Default sort ordering
     * @var array
     */
    private static $default_sort    =   [
                                            'DateHappened'      =>  'DESC'
                                        ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields                     =   parent::getCMSFields();

        $fields->fieldByName('Root.Main.FromID')->setTitle('Seller');
        $fields->fieldByName('Root.Main.ToID')->setTitle('Buyer');

        return $fields;
    }
}
