<?php

namespace App\Web\Layout;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use Page;
use App\Web\Model\DogContent;
use Leochenftw\Util;
use Leochenftw\Grid;
use App\Web\Model\DogUsage;
use Cocur\Slugify\Slugify;
use SaltedHerring\Salted\Cropper\SaltedCroppableImage;
use SaltedHerring\Salted\Cropper\Fields\CroppableImageField;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DogPage extends Page
{
    private $body_sizes =   [
        'small'     =>  '小型犬',
        'medium'    =>  '中型犬',
        'large'     =>  '大型犬'
    ];
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'DogPage';
    private static $show_in_sitetree = false;
    private static $allowed_children = [];

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'EnglishTitle'  =>  'Varchar(128)',
        'Alias'         =>  'Varchar(128)',
        'Ancestor'      =>  'Varchar(128)',
        'Origin'        =>  'Varchar(128)',
        'HeightFrom'    =>  'Int',
        'HeightTo'      =>  'Int',
        'AgeFrom'       =>  'Int',
        'AgeTo'         =>  'Int',
        'Distribution'  =>  'Varchar(128)',
        'BodySize'      =>  'Varchar(8)',
        'WeightFrom'    =>  'Int',
        'WeightTo'      =>  'Int',
        'PriceFrom'     =>  'Int',
        'PriceTo'       =>  'Int',
        'ClingyScore'   =>  'Int',
        'NoisyScore'    =>  'Int',
        'TerritorialScore'  =>  'Int',
        'FriendlyScore' =>  'Int',
        'SheddingScore' =>  'Int',
        'PrettyScore'   =>  'Int',
        'SmellScore'    =>  'Int',
        'SalivaScore'   =>  'Int',
        'ObeyScore'     =>  'Int',
        'ActiveScore'   =>  'Int',
        'ColdResistScore'   =>  'Int',
        'HeatResistScore'   =>  'Int',
        'EnergyScore'   =>  'Int',
        'Promoted'      =>  'Boolean',
        'SlideOption'   =>  'Varchar(16)'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'PageHero'  =>  SaltedCroppableImage::class,
        'Thumbnail' =>  SaltedCroppableImage::class,
        'DogHead'   =>  Image::class
    ];

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'Contents'  =>  DogContent::class
    ];

    private static $cascade_deletes = [
        'PageHero',
        'Thumbnail',
        'DogHead',
        'Contents'
    ];

    /**
     * Many_many relationship
     * @var array
     */
    private static $many_many = [
        'OriginalUsages'    =>  DogUsage::class,
        'Usages'            =>  DogUsage::class
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'PageHero',
        'Thumbnail',
        'DogHead'
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
            Grid::make('Contents', '内容板块', $this->Contents())
        );
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'EnglishTitle',
                    '英文名'
                ),
                TextField::create(
                    'Alias',
                    '别名'
                ),
                CroppableImageField::create('ThumbnailID', '列表预览图片')->setCropperRatio(1)
            ],
            'URLSegment'
        );

        $this->create_carousel_tab($fields);
        $this->create_info_tab($fields);
        $this->create_score_tab($fields);

        return $fields;
    }

    private function create_score_tab(&$fields)
    {
        $fields->addFieldsToTab(
            'Root.Parametres',
            [
                TextField::create('ClingyScore', '粘人'),
                TextField::create('NoisyScore', '吵闹'),
                TextField::create('TerritorialScore', '护家'),
                TextField::create('FriendlyScore', '友善'),
                TextField::create('SheddingScore', '掉毛'),
                TextField::create('PrettyScore', '美容'),
                TextField::create('SmellScore', '体味'),
                TextField::create('SalivaScore', '口水'),
                TextField::create('ObeyScore', '服从'),
                TextField::create('ActiveScore', '活跃'),
                TextField::create('ColdResistScore', '耐寒'),
                TextField::create('HeatResistScore', '耐热'),
                TextField::create('EnergyScore', '精力'),
            ]
        );

        $fields->fieldByName('Root.Parametres')->setTitle('犬种参数');
    }

    private function create_carousel_tab(&$fields)
    {
        $fields->addFieldToTab(
            'Root.CarouselSlideOptions',
            CheckboxField::create(
                'Promoted',
                '显示在列表页面的轮播图中'
            )
        );

        if ($this->Promoted) {
            $fields->addFieldsToTab(
                'Root.CarouselSlideOptions',
                [
                    DropdownField::create(
                        'SlideOption',
                        'Slide option',
                        [
                            'full_image'    =>  '全图片',
                            'with_txt'      =>  '狗头 + 文字'
                        ]
                    )->setEmptyString('- 请选择 -')
                ]
            );

            if ($this->SlideOption == 'with_txt') {
                $fields->addFieldsToTab(
                    'Root.CarouselSlideOptions',
                    [
                        UploadField::create(
                            'DogHead',
                            '狗头'
                        ),
                        $fields->fieldByName('Root.Main.Content')
                    ]
                );
            } elseif ($this->SlideOption == 'full_image') {
                $fields->addFieldsToTab(
                    'Root.CarouselSlideOptions',
                    [
                        CroppableImageField::create('PageHeroID', '横幅图片')->setCropperRatio(1280/740)
                    ]
                );
            }
        }

        $fields->fieldByName('Root.CarouselSlideOptions')->setTitle('轮播图设置');
    }

    private function create_info_tab(&$fields)
    {
        $fields->addFieldsToTab(
            'Root.Details',
            [
                TextField::create(
                    'Ancestor',
                    '祖先'
                ),
                TextField::create(
                    'Origin',
                    '产地'
                ),
                TextField::create(
                    'Distribution',
                    '分布'
                ),
                DropdownField::create(
                    'BodySize',
                    '体型',
                    $this->body_sizes
                )->setEmptyString('- 请选择 -'),
                TextField::create(
                    'HeightFrom',
                    '最小身高'
                ),
                TextField::create(
                    'HeightTo',
                    '最大身高'
                ),
                TextField::create(
                    'WeightFrom',
                    '最小体重'
                ),
                TextField::create(
                    'WeightTo',
                    '最大体重'
                ),
                TextField::create(
                    'AgeFrom',
                    '最小寿命'
                ),
                TextField::create(
                    'AgeTo',
                    '最大寿命'
                ),
                TextField::create(
                    'PriceFrom',
                    '最小价格'
                ),
                TextField::create(
                    'PriceTo',
                    '最高价格'
                ),
                Grid::make('OriginalUsages', '原用途', $this->OriginalUsages(), false, 'GridFieldConfig_RelationEditor'),
                Grid::make('Usages', '现用途', $this->Usages(), false, 'GridFieldConfig_RelationEditor')
            ]
        );

        $fields->fieldByName('Root.Details')->setTitle('犬种信息');
    }

    public function getData($mini = false)
    {
        $data               =   parent::getData();
        $data['dog_head']   =   $this->DogHead()->getData();
        $data['image']      =   $this->PageHero()->getData(1280, 740);
        $data['contents']   =   $this->Contents()->getData();
        $data['basic_info'] =   [
            'english_title' =>  $this->EnglishTitle,
            'alias'         =>  $this->Alias,
            'ancestor'      =>  $this->Ancestor,
            'origin'        =>  $this->Origin,
            'height_from'   =>  $this->HeightFrom,
            'height_to'     =>  $this->HeightTo,
            'age_from'      =>  $this->AgeFrom,
            'age_to'        =>  $this->AgeTo,
            'distribution'  =>  $this->Distribution,
            'body_size'     =>  !empty($this->BodySize) ? $this->body_sizes[$this->BodySize] : '未知',
            'weight_from'   =>  $this->WeightFrom,
            'weight_to'     =>  $this->WeightTo,
            'price_from'    =>  $this->PriceFrom,
            'price_to'      =>  $this->PriceTo,
            'original_usages'   =>  implode(',', $this->OriginalUsages()->column('Title')),
            'usages'        =>  implode(',', $this->Usages()->column('Title'))
        ];
        $data['scores']     =   [
            'left_col'  =>  [
                '粘人'    =>  $this->ClingyScore,
                '护家'    =>  $this->TerritorialScore,
                '掉毛'    =>  $this->SheddingScore,
                '体味'    =>  $this->SmellScore,
                '服从'    =>  $this->ObeyScore,
                '耐寒'    =>  $this->ColdResistScore,
                '精力'    =>  $this->EnergyScore
            ],
            'right_col' =>  [
                '吵闹'    =>  $this->NoisyScore,
                '友善'    =>  $this->FriendlyScore,
                '美容'    =>  $this->PrettyScore,
                '口水'    =>  $this->SalivaScore,
                '耐热'    =>  $this->HeatResistScore,
                '活跃'    =>  $this->ActiveScore,
            ]
        ];
        return $data;
    }

    public function getTileData($mini = false)
    {
        if (!empty($mini)) {
            return [
                'title'     =>  $this->Title,
                'english'   =>  $this->EnglishTitle,
                'thumbnail' =>  $this->Thumbnail()->getData(640, 640),
                'link'      =>  $this->Link()
            ];
        }

        if ($this->SlideOption == 'full_image') {
            return [
                'title' =>  $this->Title,
                'image' =>  $this->PageHero()->getData(1280, 740),
                'link'  =>  $this->Link()
            ];
        }

        return [
            'title'     =>  $this->Title,
            'dog_head'  =>  $this->DogHead()->getData(),
            'content'   =>  Util::preprocess_content($this->Content),
            'link'      =>  $this->Link()
        ];
    }

    /**
     * Event handler called before writing to the database.
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!empty($this->EnglishTitle)) {
            $slugify = new Slugify();
            $this->URLSegment   =   $slugify->slugify($this->EnglishTitle);
        }
    }

    /**
     * Event handler called after writing to the database.
     */
    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if (!$this->Contents()->exists()) {
            $i  =   1;
            foreach ($this->config()->preset_content_sections as $title) {
                $content            =   DogContent::create();
                $content->Title     =   $title;
                $content->Sort      =   $i;
                $content->DogPageID =   $this->ID;
                $content->write();
                $i++;
            }
        }
    }

}
