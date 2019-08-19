<?php

namespace App\Web\Layout;
use App\Web\Model\CarouselItem;
use Leochenftw\Util;
use Leochenftw\Grid;
use Page;
use SilverStripe\Security\Member;
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Homepage extends Page
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'Homepage';

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'Carousel'  =>  CarouselItem::class
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab(
            'Root.Banners',
            Grid::make('Carousel', '横幅', $this->Carousel())
        );
        return $fields;
    }

    public function getData()
    {
        $data               =   parent::getData();
        $data['carousel']   =   $this->Carousel()->getData();
        $data['news']       =   NewItemPage::get()->limit(2)->getTileData();
        $data['members']    =   Member::get()->limit(6)->getTileData();
        return $data;
    }
}
