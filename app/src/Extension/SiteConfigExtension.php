<?php

namespace App\Web\Extensions;

use SilverStripe\ORM\DataExtension;

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
        'FrontendEntryFile' =>  'Varchar(1024)'
    ];

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
