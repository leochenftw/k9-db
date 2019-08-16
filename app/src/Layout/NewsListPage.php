<?php

namespace App\Web\Layout;
use Page;
use SilverStripe\Forms\TextField;
use SilverStripe\Lumberjack\Model\Lumberjack;
use App\Web\Extension\LumberjackExtension;
use SilverStripe\Control\Controller;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class NewsListPage extends Page
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'NewsListPage';

    /**
     * Defines extension names and parameters to be applied
     * to this object upon construction.
     * @var array
     */
    private static $extensions = [
        Lumberjack::class,
        LumberjackExtension::class
    ];

    private static $allowed_children = [
        NewItemPage::class
    ];

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'PageSize'  =>  'Int'
    ];

    /**
     * Add default values to database
     * @var array
     */
    private static $defaults = [
        'PageSize'  =>  20
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
            TextField::create(
                'PageSize',
                '每页显示新闻数量'
            ),
            'Content'
        );
        return $fields;
    }

    public function getData()
    {
        $ctrler         =   Controller::curr();
        $is_mini        =   $ctrler->request->getVar('mini');

        if (!empty($is_mini)) {
            return $this->get_news($ctrler->request);
        }

        $data           =   parent::getData();
        $data['news']   =   $this->get_news($ctrler->request);

        return $data;
    }

    private function get_news(&$request)
    {
        $page       =   $request->getVar('page');
        $page       =   empty($page) ? 0 : $page;
        $children   =   NewItemPage::get()->sort(['Date' => 'DESC'])->limit($this->PageSize, $page * $this->PageSize);
        $count      =   $this->Children()->count();
        return [
            'list'          =>  $children->getTileData(),
            'all_loaded'    =>  $count <= ($page + 1) * $this->PageSize,
            'count'         =>  ceil($count / $this->PageSize)
        ];
    }
}
