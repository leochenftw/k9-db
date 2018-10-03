<?php

namespace KSolution;
use SilverStripe\ORM\DataObject;
use Leochenftw\Debugger;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\Tab;
use Leochenftw\Grid;
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class Dog extends DataObject
{
    /**
     * Singular name for CMS
     * @var string
     */
    private static $singular_name = '犬只';

    /**
     * Plural name for CMS
     * @var string
     */
    private static $plural_name = '犬只';
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'             =>  'Varchar(128)',
        'Sex'               =>  'Enum("公,母")',
        'DoB'               =>  'Date',
        'ChipsSerial'       =>  'Varchar(128)',
        'CertNumber'        =>  'Varchar(128)',
        'JobTitle'          =>  'Varchar(16)',
        'DNA'               =>  'Enum("是,否")',
        'ReproTest'         =>  'Enum("通过,未通过")',
        'Breeder'           =>  'Varchar(128)',
        'OwnedBy'           =>  'Varchar(128)'
    ];

    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'Dog';

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'CertCopy'          =>  'SilverStripe\Assets\Image',
        'Breed'             =>  'KSolution\Breed',
        'Portrait'          =>  'SilverStripe\Assets\Image',
        'Mother'            =>  'KSolution\Dog',
        'Father'            =>  'KSolution\Dog'
    ];

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'OwnerHistory'      =>  'KSolution\TradingRecord',
        'HipElbowScores'    =>  'KSolution\HipElbowScore',
        'Awards'            =>  'KSolution\Award',
        'Videos'            =>  'KSolution\Video',
        'FatherOf'          =>  'KSolution\Dog.Father',
        'MotherOf'          =>  'KSolution\Dog.Mother'
    ];

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = [
        'Photos'            =>  'SilverStripe\Assets\Image'
    ];

    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->fieldByName('Root.Main.Sex')->setEmptyString('- 请选择 -');
        $fields->fieldByName('Root.Main.ReproTest')->setEmptyString('- 请选择 -');

        $fields->fieldByName('Root.Main.Title')->setTitle('姓名');
        $fields->fieldByName('Root.Main.Sex')->setTitle('性别');
        $fields->fieldByName('Root.Main.DNA')->setTitle('DNA检测');
        $fields->fieldByName('Root.Main.OwnedBy')->setTitle('犬主人');
        $fields->fieldByName('Root.Main.DoB')->setTitle('出生日期');
        $fields->fieldByName('Root.Main.ChipsSerial')->setTitle('芯片号');
        $fields->fieldByName('Root.Main.CertNumber')->setTitle('证书号');

        $fields->fieldByName('Root.Main.JobTitle')->setTitle('工作头衔');
        $fields->fieldByName('Root.Main.ReproTest')->setTitle('繁殖测试');
        $fields->fieldByName('Root.Main.Breeder')->setTitle('繁殖人');
        $fields->fieldByName('Root.Main.CertCopy')->setTitle('证书扫描件');

        $fields->fieldByName('Root.Main')->setTitle('基本信息');

        if ($this->exists()) {
            $fields->fieldByName('Root.OwnerHistory')->setTitle('交易记录');
            $fields->fieldByName('Root.HipElbowScores')->setTitle('髋肘检测记录');
            $fields->fieldByName('Root.Awards')->setTitle('获奖记录');

            $fields->addFieldsToTab(
                'Root.PhotosAndVideos',
                [
                    $fields->fieldByName('Root.Photos.Photos'),
                    LiteralField::create('Separator', '<div style="height: 2em; margin-bottom: 2em;"></div>'),
                    $fields->fieldByName('Root.Videos.Videos')
                ]
            );

            $fields->fieldByName('Root.PhotosAndVideos')->setTitle('照片视频');
        }

        $fields->fieldByName('Root.Main.BreedID')->setTitle('种类')->setEmptyString('- 请选择 -');
        $fields->fieldByName('Root.Main.Portrait')->setTitle('犬肖像');

        $bitch              =   $fields->fieldByName('Root.Main.MotherID')->setTitle('母犬')->setEmptyString('- 请选择 -');
        $b_source           =   Dog::get()->filter(['Sex' => '母']);

        if ($this->exists()) {
            $b_source       =   $b_source->exclude(['ID' => $this->ID]);
        }

        $bitch->source      =   $b_source->map('ID', 'Title');

        $male_dog           =   $fields->fieldByName('Root.Main.FatherID')->setTitle('父犬')->setEmptyString('- 请选择 -');
        $m_source           =   Dog::get()->filter(['Sex' => '公']);

        if ($this->exists()) {
            $m_source       =   $m_source->exclude(['ID' => $this->ID]);
        }

        $male_dog->source   =   $m_source->map('ID', 'Title');

        if ($this->exists()) {
            $fields->removeByName([
                'Photos',
                'Videos',
                'MotherOf',
                'FatherOf'
            ]);

            $cert_tab       =   Tab::create('Certificate', '鉴定证书');
            $fields->insertAfter('Main', $cert_tab);

            $fields->addFieldsToTab(
                'Root.Certificate',
                [
                    $fields->fieldByName('Root.Main.CertNumber'),
                    $fields->fieldByName('Root.Main.CertCopy')
                ]
            );

            $mother_of      =   $this->MotherOf()->column();
            $father_of      =   $this->FatherOf()->column();
            $merge          =   array_merge($mother_of, $father_of);

            if (!empty($merge)) {
                $fields->addFieldsToTab(
                    'Root.Descendants',
                    [
                        $grid   =   Grid::make('Descendants', '子嗣', Dog::get()->filter(['ID' => $merge]), false, 'GridFieldConfig_RecordViewer')
                    ]
                );

                $grid->setTitle(null);
            } else {
                $fields->addFieldsToTab(
                    'Root.Descendants',
                    [
                        LiteralField::create('EmptyContent', '<h2 class="title is-2">- 未有子嗣 -</h2>')
                    ]
                );
            }

            $fields->fieldByName('Root.Descendants')->setTitle('子嗣');
            $fields->fieldByName('Root.OwnerHistory.OwnerHistory')->setTitle(null);
            $fields->fieldByName('Root.PhotosAndVideos.Videos')->setTitle(null);
            $fields->fieldByName('Root.PhotosAndVideos.Photos')->setTitle(null);
            $fields->fieldByName('Root.HipElbowScores.HipElbowScores')->setTitle(null);
            $fields->fieldByName('Root.Awards.Awards')->setTitle(null);
        } else {
            $fields->removeByName([
                'CertCopyID',
                'CertNumber'
            ]);
        }



        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}