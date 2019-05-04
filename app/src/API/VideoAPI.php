<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Security\Member;
use SilverStripe\Security\IdentityStore;
use SilverStripe\Core\Injector\Injector;
use Leochenftw\Debugger;
use KSolution\PhotosetCategory;
use KSolution\VideoRecord;

class VideoAPI extends RestfulController
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
            if ($video = $member->Videos()->byID($request->Param('ID'))) {
                $video->delete();
                return '视频已删除';
            }

            return $this->httpError(404, '视频不存在!');
        }

        return $this->httpError(403, '请先登录!');
    }

    public function get($request)
    {
        $list   =   $this->member->Videos();
        if ($id = $request->Param('ID')) {
            $list   =   $list->byID($id);
        }

        return $list->getData();
    }

    public function post($request)
    {
        if (($youku = $request->postVar('youku_id'))) {
            $title          =   $request->postVar('title');
            $desc           =   $request->postVar('description');
            $category       =   $request->postVar('category');

            $video          =   VideoRecord::create();
            $video->YoukuID =   $youku;

            if (!empty($title)) {
                $video->Title       =   $title;
            }

            if (!empty($desc)) {
                $video->Description =   $desc;
            }

            if (!empty($category)) {
                $category_item      =   PhotosetCategory::get()->filter(['Title' => $category])->first();
                if (empty($category_item)) {
                    $category_item          =   PhotosetCategory::create();
                    $category_item->Title   =   $category;
                    $category_item->write();
                }

                $video->CategoryID  =   $category_item->ID;
            }

            $video->MemberID  =   $this->member->ID;
            $video->write();

            return $video->ID;
        }

        return $this->httpError(400, 'missing param(s)');
    }
}
