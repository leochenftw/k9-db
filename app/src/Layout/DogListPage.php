<?php

namespace App\Web\Layout;
use SilverStripe\Lumberjack\Model\Lumberjack;
use SilverStripe\Forms\TextField;
use App\Web\Extension\LumberjackExtension;
use SilverStripe\Control\Controller;
use Page;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DogListPage extends Page
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'DogListPage';

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
        DogPage::class
    ];

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'PageSize'  =>  'Int'
    ];

    public function getData()
    {
        $ctrler         =   Controller::curr();
        $is_mini        =   $ctrler->request->getVar('mini');

        if (!empty($is_mini)) {
            return $this->get_dogs($ctrler->request);
        }

        $data           =   parent::getData();
        $data['breed_carousel'] =   DogPage::get()->filter(['Promoted' => true])->limit(5)->getTileData();
        $data['breeds'] =   $this->get_dogs($ctrler->request);
        return $data;
    }

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
                '每页显示犬种数量'
            ),
            'URLSegment'
        );
        return $fields;
    }

    private function get_dogs(&$request)
    {
        $page       =   !empty($request) ? $request->getVar('page') : null;
        $page       =   empty($page) ? 0 : $page;
        $children   =   $this->Children()->limit($this->PageSize, $page * $this->PageSize);
        $count      =   $this->Children()->count();
        return [
            'list'          =>  $children->getTileData(true),
            'all_loaded'    =>  $count <= ($page + 1) * $this->PageSize,
            'count'         =>  ceil($count / $this->PageSize)
        ];
    }
}
