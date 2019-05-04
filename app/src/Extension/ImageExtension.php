<?php

namespace App\Web\Extensions;
use SilverStripe\ORM\DataExtension;

class ImageExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Sort'      =>  'Int'
    ];

    public function getData()
    {
        return  [
            'id'    =>  $this->owner->ID,
            'title' =>  $this->owner->Title,
            'url'   =>  $this->owner->getURL(),
            'thumb' =>  $this->owner->Fill(320,320)->getURL()
        ];
    }
}
