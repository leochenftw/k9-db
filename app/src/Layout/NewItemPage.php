<?php

namespace App\Web\Layout;
use Page;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\TextField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\DateField;
use SilverStripe\Assets\Image;
use Leochenftw\Debugger;
use Leochenftw\Util;
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class NewItemPage extends Page
{
    /**
     * Defines whether a page can be in the root of the site tree
     * @var boolean
     */
    private static $can_be_root = false;
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'NewItemPage';
    private static $show_in_sitetree = false;
    private static $allowed_children = [];

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Date'              =>  'Date',
        'Excerpt'           =>  'Text',
        'CoverCopyright'    =>  'Varchar(512)'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'CoverImage'    =>  Image::class
    ];

    /**
     * Default sort ordering
     * @var array
     */
    private static $default_sort = ['Date' => 'DESC'];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'CoverImage'
    ];

    public function populateDefaults()
    {
        $this->Date =   time();
    }

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->addFieldsToTab(
            'Root.Main',
            [
                DateField::create('Date', '发布日期'),
                UploadField::create('CoverImage', '文章封面')->setDescription($this->CoverImage()->exists() ? ('当前版权信息: ' . $this->CoverImage()->Copyright) : ''),
                TextField::create('CoverCopyright', ($this->CoverImage()->exists() ? '修改' : '添加') . '文章封面版权')
            ],
            'URLSegment'
        );

        $fields->addFieldToTab(
            'Root.Main',
            TextareaField::create(
                'Excerpt',
                '简介'
            )->setDescription('用于首页和新闻列表页.'),
            'Content'
        );
        return $fields;
    }

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!empty($this->record['CoverImageID']) && !empty($this->record['CoverCopyright'])) {
            $this->CoverImage()->Copyright  =   $this->CoverCopyright;
            $this->CoverImage()->write();
            $this->CoverCopyright           =   null;
        }
    }

    public function getData()
    {
        $data               =   parent::getData();
        $data['date']       =  $this->Date;
        $data['excerpt']    =   $this->Excerpt;
        $data['cover']      =  $this->CoverImage()->getData();
        $data['siblings']   =   array_values(array_filter([
            $this->getPrevItem(),
            $this->getNextItem()
        ]));

        return $data;
    }

    public function getTileData()
    {
        $image              =   $this->CoverImage()->Fill(526,330);
        $data               =   [];
        $data['id']         =   $this->ID;
        $data['title']      =   $this->Title;
        $data['date']       =   $this->Date;
        $data['image']      =   $image ? $image->getAbsoluteURL() : null;
        $data['content']    =   $this->Excerpt;
        $data['day']        =   date('d', strtotime($this->Date));
        $data['month']      =   $this->translate_month();
        $data['link']       =   [
            'url'   =>  rtrim($this->Link(), '/'),
            'label' =>  '阅读'
        ];

        return $data;
    }

    private function translate_month()
    {
        $months =   ['壹', '贰', '弎', '肆', '伍', '陆', '柒', '捌', '玖', '拾', '拾壹', '拾贰'];
        $month  =   date('m', strtotime($this->Date)) - 1;

        return $months[$month];
    }

    private function getPrevItem()
    {
        $filter =   [
            'Date:GreaterThanOrEqual' => strtotime($this->Date),
            'ID:not' => $this->ID,
            'Sort:LessThan' => $this->Sort
        ];

        if ($prev = (__CLASS__)::get()->filter($filter)->sort(['Date' => 'ASC'])->first()) {
            return $prev->getTileData();
        }

        return null;
    }

    private function getNextItem()
    {
        $filter =   ['Date:LessThanOrEqual' => strtotime($this->Date), 'ID:not' => $this->ID];

        if ($next = (__CLASS__)::get()->filter($filter)->first()) {
            return $next->getTileData();
        }

        return null;
    }
}
