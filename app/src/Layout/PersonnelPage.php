<?php

namespace App\Web\Layout;

use SilverStripe\Security\Member;
use SilverStripe\Control\Controller;
use Page;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class PersonnelPage extends Page
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'PersonnelPage';
    /**
     * CMS Fields
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'Content'
        ]);
        return $fields;
    }

    public function getData()
    {
        $controller =   Controller::curr();
        $data       =   parent::getData();
        if ($member_id = $controller->request->param('ID')) {
            return $this->getMemberData($member_id, $data);
        }

        $data['members']    =   Member::get()->limit(6)->getTileData();

        return $data;
    }

    private function getMemberData($id, &$data)
    {
        if ($member = Member::get()->byID($id)) {
            $data['targeted_member']    =   $member->getData();
        }
        return $data;
    }
}
