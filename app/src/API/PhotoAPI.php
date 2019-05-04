<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use Leochenftw\Debugger;
use KSolution\PhotosetCategory;
use KSolution\Photoset;

class PhotoAPI extends RestfulController
{
    private $member =   null;
    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = [
        'delete'    =>  '->isAuthenticated',
        'get'       =>  '->isAuthenticated',
        'post'      =>  '->isAuthenticated'
    ];

    public function isAuthenticated()
    {
        if ($this->member = Member::currentUser()) {
            return true;
        }
        return false;
    }

    public function delete($request)
    {
        if ($member = Member::currentUser()) {
            if ($set = $member->Photosets()->byID($request->Param('ID'))) {
                $set->delete();
                return '照片集已删除';
            }

            return $this->httpError(404, '照片集不存在!');
        }

        return $this->httpError(403, '请先登录!');
    }

    public function get($request)
    {
        $list   =   $this->member->Photosets();
        if ($id = $request->Param('ID')) {
            $list   =   $list->byID($id);
        }

        return $list->getData();
    }

    public function post($request)
    {
        if ($request->Param('Action') == 'delete') {
            return $this->delete($request);
        }

        if (($id = $request->postVar('id')) && ($photo = $request->postVar('image'))) {
            $set    =   Photoset::get()->byID($id);
            $set->digest($photo, $request->postVar('idx'));
            return $set->ID;
        } elseif (
            !empty($request->postVar('title')) ||
            !empty($request->postVar('description')) ||
            !empty($request->postVar('category'))
        ) {
            $title      =   $request->postVar('title') == 'null' ?
                            null :
                            $request->postVar('title');
            $desc       =   $request->postVar('description') == 'null' ?
                            null :
                            $request->postVar('description');
            $category   =   $request->postVar('category') == 'null' ?
                            null :
                            $request->postVar('category');
            $set        =   Photoset::create();

            if (!empty($title)) {
                $set->Title             =   $title;
            }

            if (!empty($desc)) {
                $set->Description       =   $desc;
            }

            if (!empty($category)) {
                $category_item = PhotosetCategory::get()->filter(['Title' => $category])->first();
                if (empty($category_item)) {
                    $category_item          =   PhotosetCategory::create();
                    $category_item->Title   =   $category;
                    $category_item->write();
                }

                $set->CategoryID        =   $category_item->ID;
            }

            $set->MemberID  =   $this->member->ID;
            $set->write();

            return $set->ID;
        }

        return $this->httpError(400, 'missing param');
    }
}
