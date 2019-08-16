<?php

namespace App\Web\Extension;

use SilverStripe\ORM\DataExtension;

class SaltedImageExtension extends DataExtension
{
    public function getData($width, $height)
    {
        if (!$this->owner->exists() || !$this->owner->Original()->exists() || empty($cropped = $this->owner->getCropped())) {
            return null;
        }

        return  [
            'id'        =>  $this->owner->ID,
            'title'     =>  $this->owner->Title,
            'url'       =>  $cropped->Fill($width, $height)->getAbsoluteURL()
        ];
    }
}
