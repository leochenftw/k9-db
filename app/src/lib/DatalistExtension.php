<?php

namespace App\Web\Extensions;
use SilverStripe\ORM\DataExtension;

class DatalistExtension extends DataExtension
{
    public function getData()
    {
        $list   =   [];
        foreach ($this->owner as $item) {
            if ($item->hasMethod('getData')) {
                $list[] =   $item->getData();
            }
        }

        return $list;
    }
}
