<?php

namespace App\Web\Extensions;
use SilverStripe\ORM\DataExtension;

class DatalistExtension extends DataExtension
{
    public function getData($mini = false)
    {
        $list   =   [];
        foreach ($this->owner as $item) {
            if ($item->hasMethod('getData')) {
                $list[] =   $item->getData($mini);
            }
        }

        return $list;
    }

    public function getTileData($mini = false)
    {
        $list   =   [];
        foreach ($this->owner as $item) {
            if ($item->hasMethod('getTileData')) {
                $list[] =   $item->getTileData($mini);
            }
        }

        return $list;
    }
}
