<?php

namespace App\Web\Extensions;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;

class ImageExtension extends DataExtension
{
    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Sort'      =>  'Int',
        'Copyright' =>  'Varchar(512)'
    ];

    public function getData()
    {
        if (!$this->owner->exists()) {
            return null;
        }

        return  [
            'id'        =>  $this->owner->ID,
            'title'     =>  $this->owner->Title,
            'url'       =>  $this->owner->getAbsoluteURL(),
            'thumb'     =>  $this->owner->Fill(320,320)->getAbsoluteURL(),
            'copyright' =>  $this->owner->Copyright
        ];
    }

    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        $fields->addFieldToTab(
            'Root.Main',
            TextField::create(
                'Copyright',
                'Copyright'
            )
        );
        return $fields;
    }
}
