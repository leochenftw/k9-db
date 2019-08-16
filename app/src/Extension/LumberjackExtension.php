<?php

namespace App\Web\Extension;
use SilverStripe\Lumberjack\Model\Lumberjack;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use UndefinedOffset\SortableGridField\Forms\GridFieldSortableRows;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Leochenftw\Debugger;
use App\Web\Layout\NewsLandingPage;
use App\Web\Layout\Script;
use App\Web\Layout\ScriptCategoryPage;
use SilverStripe\Versioned\Versioned;

class LumberjackExtension extends DataExtension
{
    /**
     * Update Fields
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $owner = $this->owner;
        if ($owner->hasExtension(Lumberjack::class)) {
            $children   =   $fields->fieldByName('Root.ChildPages.ChildPages');
            if (empty($children)) return $fields;
            
            $fields->removeByName([
                'ChildPages'
            ]);
            $fields->removeByName([
                'ChildPages'
            ]);

            $config     =   $children->getConfig();

            if ($owner->ClassName != NewsLandingPage::class && $owner->ClassName != ScriptCategoryPage::class) {
                $config->addComponent($sortable = new GridFieldSortableRows('Sort'));
                $sortable->setUpdateVersionedStage('Live');
            } elseif ($owner->ClassName == NewsLandingPage::class) {
                $list   =   $children->getList();
                $children->setList($list->sort(['ID' => 'DESC']));
            } elseif ($owner->ClassName == ScriptCategoryPage::class) {
                $list   =   $children->getList();
                $list   =   Versioned::get_by_stage(Script::class, 'Stage')->filter(['ID' => $list->column('ID')]);
                $children->setList($list->sort(['SortingTitle' => 'ASC']));
            }

            $fields->addFieldToTab(
                'Root.ChildPages',
                $children
            );
        }
        return $fields;
    }
}
