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
class Photoset extends DataObject
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'         =>  'Varchar(256)',
        'Description'   =>  'Varchar(2048)'
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
    private static $table_name = 'Photoset';
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '照片集';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = '照片集';

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'Photos'
    ];

    private static $cascade_deletes = [
        'Photos'
    ];

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = [
        'Photos'    =>  Image::class
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields     =   parent::getCMSFields();
        $fields->removeByName([
            'Photos'
        ]);
        $fields->addFieldsToTab(
            'Root.Photos',
            [
                Grid::make('Photos', 'Photos', $this->Photos(), false)
            ]
        );
        return $fields;
    }

    public function digest(&$data, $i)
    {
        if ($member = Member::currentUser()) {
            $fold           =   Folder::find_or_make('MemberContributedImages/' . $member->ID . '/DogPhotos');
            $img            =   Image::create();

            $filename       =   substr(sha1($data['name']), 0, 16);
            $segments       =   explode('.', $data['name']);
            $ext            =   count($segments) > 1 ? $segments[count($segments) - 1] : 'jpg';

            $img->setFromLocalFile($data['tmp_name'], $filename . '.' . $ext);
            $img->ParentID  =   $fold->ID;
            $img->Sort      =   $i;
            $img->write();
            AssetAdmin::create()->generateThumbnails($img);
            $img->publishSingle();
            $this->Photos()->add($img);
        }
    }

    public function getData()
    {
        return  [
            'id'            =>  $this->ID,
            'title'         =>  $this->Title,
            'date'          =>  $this->Created,
            'description'   =>  $this->Description,
            'category'      =>  $this->Category()->exists() ? $this->Category()->Title : null,
            'thumb'         =>  $this->Photos()->exists() ? $this->Photos()->sort(['Sort' => 'ASC'])->first()->ScaleWidth(320)->getURL() : null,
            'photos'        =>  $this->Photos()->sort(['Sort' => 'ASC'])->getData()
        ];
    }

    public function getTileData()
    {
        return  [
            'id'            =>  $this->ID,
            'title'         =>  $this->Title,
            'date'          =>  $this->Created,
            'cover'         =>  $this->Photos()->sort(['Sort' => 'ASC'])->first() ?
                                $this->Photos()->sort(['Sort' => 'ASC'])->first()->getData() : null
        ];
    }
}
