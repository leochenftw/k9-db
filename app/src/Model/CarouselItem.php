<?php

namespace App\Web\Model;
use App\Web\Layout\Homepage;
use gorriecoe\LinkField\LinkField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Assets\Image;
use gorriecoe\Link\Models\Link;
use SilverStripe\ORM\DataObject;
use App\Web\Extensions\SortOrderExtension;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class CarouselItem extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'CarouselItem';
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'     =>  'Text',
        'Subtitle'  =>  'Varchar(128)',
        'Type'      =>  'Varchar(16)',
        'Start'     =>  'Date',
        'End'       =>  'Date'
    ];

    /**
     * Defines extension names and parameters to be applied
     * to this object upon construction.
     * @var array
     */
    private static $extensions = [
        SortOrderExtension::class
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'Image'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Link'  =>  Link::class,
        'Image' =>  Image::class,
        'Page'  =>  Homepage::class
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'Type',
                '类型',
                [
                    'event' =>  '活动赛事',
                    'news'  =>  '新闻'
                ]
            ),
            'Start'
        );
        $fields->removeByName([
            'LinkID',
            'PageID'
        ]);

        $fields->addFieldToTab(
            'Root.Main',
            LinkField::create(
                'Link',
                '链接到页面',
                $this
            )
        );

        return $fields;
    }

    public function getData()
    {
        $data   =   [
            'id'        =>  $this->ID,
            'title'     =>  $this->Title,
            'type'      =>  $this->Type,
            'subtitle'  =>  $this->Subtitle,
            'link'      =>  $this->Link()->getData(),
            'image'     =>  $this->Image()->getData()
        ];

        if ($this->Type == 'event') {
            $data['dates']  =   [
                'start' =>  date('m.d', strtotime($this->Start)),
                'end'   =>  date('m.d', strtotime($this->End))
            ];
        }

        return $data;
    }
}
