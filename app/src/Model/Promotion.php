<?php

namespace App\Web\Model;

use gorriecoe\LinkField\LinkField;
use gorriecoe\Link\Models\Link;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\Director;
use Leochenftw\Util;
use Leochenftw\Debugger;
use Page;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Promotion extends DataObject
{
    private static $promo_positions =   [
        'page_middle'   =>  '页面中部 (1/1)',
        'page_bottom'   =>  '页面底部 (1/2)'
    ];

    private static $image_positions =   [
        'left'  =>  '图文 / 图片居左',
        'right' =>  '图文 / 图片居右',
        'full'  =>  '图片全占'
    ];
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'Promotion';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'         =>  'Varchar(128)',
        'Content'       =>  'Text',
        'Start'         =>  'Date',
        'End'           =>  'Date',
        'Position'      =>  'Varchar(64)',
        'ImagePosition' =>  'Varchar(16)',
        'Hash'          =>  'Varchar(64)'
    ];

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'Clicks'    =>  PromoClick::class
    ];

    private static $cascade_deletes = ['Clicks'];

    /**
     * Default sort ordering
     * @var array
     */
    private static $default_sort = ['Start' => 'ASC', 'End' => 'ASC'];

    public function populateDefaults()
    {
        $this->Start    =   date('Y-m-d', time());
    }

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Image' =>  Image::class,
        'Link'  =>  Link::class
    ];

    /**
     * Belongs_many_many relationship
     * @var array
     */
    private static $belongs_many_many = [
        'Pages' =>  Page::class
    ];

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = [
        'Title'             =>  '标题',
        'Link.getLinkURL'   =>  '广告链接',
        'Start'             =>  '开始',
        'End'               =>  '结束',
        'getTotalClicks'    =>  '总点击量',
        'getValidClicks'    =>  '有效点击',
        'getDisPages'       =>  '投放页面',
        'getPromoPos'       =>  '页面位置'
    ];

    /**
     * Defines a default list of filters for the search context
     * @var array
     */
    private static $searchable_fields = [
        'Title'
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'Image'
    ];

    private static $indexes = [
        'Hash'  =>  true
    ];

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (empty($this->Hash)) {
            $this->Hash =   sha1(time() . rand());
        }
    }

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'LinkID',
            'PageID',
            'Hash'
        ]);

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'Position',
                '投放位置',
                self::$promo_positions
            )->setEmptyString('- 选择投放位置 -'),
            'Position'
        );

        $fields->addFieldToTab(
            'Root.Main',
            DropdownField::create(
                'ImagePosition',
                '广告类型',
                self::$image_positions
            )->setEmptyString('- 选择图文位置 -'),
            'Position'
        );

        $fields->addFieldToTab(
            'Root.Main',
            LinkField::create(
                'Link',
                '广告链接',
                $this
            )
        );

        if (!empty($this->Hash)) {
            $fields->fieldbyName('Root.Main.Title')->setDescription('<em>Hash: ' . $this->Hash . '</em>');
        }


        return $fields;
    }

    public function getData()
    {
        if ($link = $this->Link()->getData()) {
            $link['url']    =   Director::absoluteBaseURL() . 'promo-handler/' . $this->Hash;
        }

        return [
            'id'        =>  $this->ID,
            'title'     =>  $this->Title,
            'content'   =>  Util::preprocess_content($this->Content),
            'promo_pos' =>  $this->Position,
            'image_pos' =>  $this->ImagePosition,
            'image'     =>  $this->Image()->getData(),
            'link'      =>  $link
        ];
    }

    public function getTotalClicks()
    {
        $count  =   0;
        foreach ($this->Clicks() as $click) {
            $count += $click->Contribution;
        }

        return $count;
    }

    public function getValidClicks()
    {
        return $this->Clicks()->count();
    }

    public function getDisPages()
    {
        if ($this->Pages()->exists()) {
            return implode(', ', $this->Pages()->column('Title'));
        }
        return '未投放';
    }

    public function getPromoPos()
    {
        if (!empty(self::$promo_positions[$this->Position])) {
            return self::$promo_positions[$this->Position];
        }

        return '未指定页面位置';
    }

}
