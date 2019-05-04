<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Folder;
use KSolution\PhotosetCategory;
use Leochenftw\Grid;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class VideoRecord extends DataObject
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'         =>  'Varchar(256)',
        'Description'   =>  'Varchar(2048)',
        'YoukuID'       =>  'Varchar(512)'
    ];
    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'Member'    =>  Member::class,
        'Category'  =>  PhotosetCategory::class
    ];

    /**
     * Default sort ordering
     * @var array
     */
    private static $default_sort = ['Created' => 'DESC'];
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'VideoRecord';
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

    public function getData()
    {
        return  [
            'id'            =>  $this->ID,
            'title'         =>  $this->Title,
            'date'          =>  $this->Created,
            'description'   =>  $this->Description == 'null' ? null : $this->Description,
            'youku_id'      =>  $this->YoukuID,
            'category'      =>  $this->Category()->exists() ? $this->Category()->Title : null
        ];
    }
}
