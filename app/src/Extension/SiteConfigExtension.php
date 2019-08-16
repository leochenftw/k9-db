<?php

namespace App\Web\Extensions;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use App\Web\Model\FooterLink;
use Leochenftw\Util;
use Leochenftw\Grid;

/**
 * @file SiteConfigExtension
 *
 * Extension to provide Open Graph tags to site config.
 */
class SiteConfigExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'FrontendEntryFile' =>  'Varchar(1024)',
        'ContactEmail'      =>  'Varchar(256)',
        'ContactPhone'      =>  'Varchar(16)',
        'ContactWechat'     =>  'Varchar(32)',
        'WelcomeMessage'    =>  'Varchar(128)'
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'QRCode'    =>  Image::class
    ];

    /**
     * Has_many relationship
     * @var array
     */
    private static $has_many = [
        'FooterLinks'   =>  FooterLink::class
    ];

    /**
     * Relationship version ownership
     * @var array
     */
    private static $owns = [
        'QRCode'
    ];

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Contact us',
            [
                EmailField::create(
                    'ContactEmail',
                    '工作邮箱'
                ),
                TextField::create(
                    'ContactPhone',
                    '联系电话'
                ),
                TextField::create(
                    'ContactWechat',
                    '微信号'
                ),
                UploadField::create(
                    'QRCode',
                    '二维码'
                )
            ]
        );

        $fields->addFieldToTab(
            'Root.Footer Links',
            Grid::make('FooterLinks', 'FooterLinks', $this->owner->FooterLinks())
        );

        $fields->addFieldsToTab(
            'Root.Frontend',
            [
                TextField::create('FrontendEntryFile', 'Path(s) to frontend\'s index.hmlt file')
            ]
        );

        $fields->addFieldToTab(
            'Root.Main',
            TextField::create(
                'WelcomeMessage',
                '页面顶部欢迎信息'
            )
        );

        return $fields;
    }

    public function getData()
    {
        return [
            'title'             =>  $this->owner->Title,
            'contact'           =>  [
                'email'     =>  $this->owner->ContactEmail,
                'phone'     =>  $this->owner->ContactPhone,
                'wechat'    =>  [
                    'official_account'  =>  $this->owner->ContactWechat,
                    'qr_code'           =>  $this->owner->QRCode()->getData()
                ]
            ],
            'menu'              =>  $this->owner->FooterLinks()->getData(),
            'welcome_message'   =>  $this->owner->WelcomeMessage
        ];
    }

    private function read_vue_entry_file()
    {
        $file   =   $this->owner->FrontendEntryFile;
        if (!empty($file)) {
            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }

        return false;
    }

    public function getVueCSS()
    {
        if ($file = $this->read_vue_entry_file()) {
            $style_pattern  =   "/\<link href=\"(.*?)\" rel=\"stylesheet\">/i";
            preg_match_all($style_pattern, $file, $matches);
            $styles         =   count($matches) > 0 ? $matches[0] : [];
            if (empty($styles)) {
                $style_pattern  =   "/\<link href=(.*?) rel=stylesheet>/i";
                preg_match_all($style_pattern, $file, $matches);
                $styles         =   count($matches) > 0 ? $matches[0] : [];
            }

            return implode("\n", $styles);
        }

        return null;
    }

    public function getVueJS()
    {
        if ($file = $this->read_vue_entry_file()) {
            $script_pattern =   "/\<script type=\"text\/javascript\" src=\"(.*?)\"\>\<\/script\>/i";
            preg_match_all($script_pattern, $file, $matches);
            $scripts        =   count($matches) > 0 ? $matches[0] : [];

            if (empty($scripts)) {
                $script_pattern =   "/\<script type=text\/javascript src=(.*?)\>\<\/script\>/i";
                preg_match_all($script_pattern, $file, $matches);
                $scripts        =   count($matches) > 0 ? $matches[0] : [];
            }

            return implode("\n", $scripts);
        }

        return null;
    }
}
