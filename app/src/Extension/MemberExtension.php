<?php

namespace App\Web\Extensions;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use Leochenftw\Utils\SMS;
use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Controller\AssetAdmin;
use SaltedHerring\Salted\Cropper\SaltedCroppableImage;
use SaltedHerring\Salted\Cropper\Fields\CroppableImageField;
use SilverStripe\Assets\Folder;
use Leochenftw\Debugger;
use Leochenftw\Grid;
use KSolution\Photoset;
use KSolution\VideoRecord;
use KSolution\Dog;
use App\Web\Layout\PersonnelPage;
use App\Web\Email\ActivationEmail;
use App\Web\Email\OneoffPassEmail;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class MemberExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Type'              =>  'Enum("Normal,Pro")',
        'Username'          =>  'Varchar(32)',
        'QQ'                =>  'Varchar(32)',
        'WeChat'            =>  'Varchar(128)',
        'Phone'             =>  'Varchar(32)',
        'Address'           =>  'Text',
        'ValidationKey'     =>  'Varchar(4)',
        'Identity'          =>  'Varchar(24)',
        'Province'          =>  'Varchar(16)',
        'City'              =>  'Varchar(16)',
        'Suburb'            =>  'Varchar(24)',
        'Company'           =>  'Varchar(64)',
        'JobTitle'          =>  'Varchar(32)',
        'Occupation'        =>  'Varchar(64)',
        'YearsExp'          =>  'Int',
        'Viewed'            =>  'Int',
        'OneoffToken'       =>  'Varchar(128)',
        'TokenGenTime'      =>  'Datetime'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'KennelCert'    =>  File::class,
        'Resume'        =>  File::class,
        'TrainerCert'   =>  File::class,
        'Portrait'      =>  SaltedCroppableImage::class
    ];

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'Photosets'     =>  Photoset::class,
        'Videos'        =>  VideoRecord::class,
        'Dogs'          =>  Dog::Class
    ];

    private static $cascade_deletes = [
        'KennelCert',
        'Resume',
        'TrainerCert',
        'Portrait'
    ];

    private static $indexes =   [
        'Username' => [
            'type'      =>  'unique',
            'columns'   =>  ['Username'],
        ],
        'Phone' => [
            'type'      =>  'unique',
            'columns'   =>  ['Phone'],
        ]
    ];

    public function populateDefaults()
    {
        $digits                     =   4;
        $this->owner->ValidationKey =   str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);
    }

    public function send_sms()
    {
        SMS::send($this->owner->Phone, $this->owner->ValidationKey);
    }

    public function send_confirmation_email()
    {
        $email  =   ActivationEmail::create($this->owner);

        return $email->send();
    }

    public function send_oneoff_pass_email()
    {
        $this->GenerateOneOffToken();

        $email  =   OneoffPassEmail::create($this->owner);
        $email->send();

        return [
            'code'      =>  200,
            'message'   =>  '通行证已经发出. 请查看电子邮箱, 并于24小时内使用. 超过24小时, 通行证将自动过期.'
        ];
    }

    public function isActivated()
    {
        return empty($this->owner->ValidationKey);
    }

    public function GenerateOneOffToken()
    {
        if ($this->owner->exists() && (empty($this->owner->OneoffToken) || strtotime($this->owner->TokenGenTime) < time() + 86400)) {
            $this->owner->OneoffToken   =   sha1($this->owner->ID . rand() . time());
            $this->owner->TokenGenTime  =   time();
            $this->owner->write();
        }
    }

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        $fields->addFieldsToTab(
            'Root.Main',
            [
                CroppableImageField::create('PortraitID', 'Portrait')->setCropperRatio(1)
            ],
            'FirstName'
        );

        if ($this->owner->exists()) {
            $fields->removeByName([
                'Photosets'
            ]);

            $fields->addFieldsToTab(
                'Root.Photosets',
                [
                    Grid::make('Photosets', 'Photosets', $this->owner->Photosets(), false)
                ]
            );
        }

        return $fields;
    }

    public function create_file($path, $file_name)
    {
        $fold           =   Folder::find_or_make('MemberFiles/' . $this->owner->ID);
        $file           =   File::create();
        $file->setFromLocalFile($path, $file_name);
        $file->ParentID =   $fold->ID;
        $file->write();

        return $file->ID;
    }

    public function create_portrait($path, $file_name, $data)
    {
        if ($this->owner->Portrait()->exists()) {
            $this->owner->Portrait()->delete();
        }

        $fold           =   Folder::find_or_make('MemberPortraits');
        $img            =   Image::create();
        $img->setFromLocalFile($path, $file_name);
        $img->ParentID  =   $fold->ID;
        $img->write();
        AssetAdmin::create()->generateThumbnails($img);
        $img->publishSingle();

        $croppable      =   SaltedCroppableImage::create();

        $croppable->OriginalID      =   $img->ID;
        $croppable->ContainerX      =   $data->container_x;
        $croppable->ContainerY      =   $data->container_y;
        $croppable->ContainerWidth  =   $data->container_width;
        $croppable->ContainerHeight =   $data->container_height;
        $croppable->CropperX        =   $data->cropper_x;
        $croppable->CropperY        =   $data->cropper_y;
        $croppable->CropperWidth    =   $data->cropper_width;
        $croppable->CropperHeight   =   $data->cropper_height;
        $croppable->write();

        $this->owner->PortraitID   =   $croppable->ID;
        $this->owner->write();
    }

    public function update_portrait($data, $id)
    {
        if ($this->owner->PortraitID != $id) {
            return $this->httpError(403, 'Portrait update failed');
        }

        $croppable                  =   $this->owner->Portrait();
        $croppable->ContainerX      =   $data->container_x;
        $croppable->ContainerY      =   $data->container_y;
        $croppable->ContainerWidth  =   $data->container_width;
        $croppable->ContainerHeight =   $data->container_height;
        $croppable->CropperX        =   $data->cropper_x;
        $croppable->CropperY        =   $data->cropper_y;
        $croppable->CropperWidth    =   $data->cropper_width;
        $croppable->CropperHeight   =   $data->cropper_height;
        $croppable->write();
    }

    public function getTileData()
    {
        return [
            'nickname'  =>  $this->owner->Username,
            'portrait'  =>  $this->owner->Portrait()->getData(440, 440),
            'link'      =>  PersonnelPage::get()->first()->Link() . $this->owner->ID,
            'viewed'    =>  $this->owner->Viewed
        ];
    }

    public function getData()
    {
        $data   =   [
            'fullname'  =>  $this->owner->Surname . $this->owner->FirstName,
            'nickname'  =>  $this->owner->Username,
            'mobile'    =>  $this->owner->Phone,
            'identity'  =>  $this->owner->Identity,
            'wechat'    =>  $this->owner->WeChat,
            'email'     =>  filter_var($this->owner->Email, FILTER_VALIDATE_EMAIL) ? $this->owner->Email : '',
            'type'      =>  strtolower($this->owner->Type),
            'province'  =>  empty($this->owner->Province) ? '' : $this->owner->Province,
            'city'      =>  empty($this->owner->City) ? '' : $this->owner->City,
            'suburb'    =>  empty($this->owner->Suburb) ? '' : $this->owner->Suburb,
            'portrait'  =>  $this->owner->Portrait()->exists() ? [
                'file'  =>  [
                    'id'    =>  $this->owner->Portrait()->ID,
                    'label' =>  $this->owner->Portrait()->Original()->Title,
                    'link'  =>  $this->owner->Portrait()->Original()->getAbsoluteURL()
                ],
                'cropped'           =>  $this->owner->Portrait()->getData(440, 440),
                'container_x'       =>  $this->owner->Portrait()->ContainerX,
                'container_y'       =>  $this->owner->Portrait()->ContainerY,
                'container_width'   =>  $this->owner->Portrait()->ContainerWidth,
                'container_height'  =>  $this->owner->Portrait()->ContainerHeight,
                'cropper_x'         =>  $this->owner->Portrait()->CropperX,
                'cropper_y'         =>  $this->owner->Portrait()->CropperY,
                'cropper_width'     =>  $this->owner->Portrait()->CropperWidth,
                'cropper_height'    =>  $this->owner->Portrait()->CropperHeight
            ] : null
        ];

        if (strtolower($this->owner->Type) == 'normal') {
            $data['kennel_cert']    =   $this->owner->KennelCert()->exists() ? [
                'label' =>  $this->owner->KennelCert()->Title,
                'link'  =>  $this->owner->KennelCert()->getAbsoluteURL(),
                'file'  =>  null,
                'ext'   =>  $this->owner->KennelCert()->getExtension()
            ] : null;

            $data['company']        =   $this->owner->Company;
            $data['job_title']      =   $this->owner->JobTitle;
        } elseif (strtolower($this->owner->Type) == 'pro') {
            $data['resume'] =   $this->owner->Resume()->exists() ? [
                'label' =>  $this->owner->Resume()->Title,
                'link'  =>  $this->owner->Resume()->getAbsoluteURL(),
                'file'  =>  null,
                'ext'   =>  $this->owner->Resume()->getExtension()
            ] : null;
            $data['cert'] =   $this->owner->TrainerCert()->exists() ? [
                'label' =>  $this->owner->TrainerCert()->Title,
                'link'  =>  $this->owner->TrainerCert()->getAbsoluteURL(),
                'file'  =>  null,
                'ext'   =>  $this->owner->TrainerCert()->getExtension()
            ] : null;

            $data['occupation']     =   $this->owner->Occupation;
            $data['years']          =   $this->owner->YearsExp;
        }

        return $data;
    }

}
