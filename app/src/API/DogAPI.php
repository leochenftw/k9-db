<?php

namespace Leochenftw\API;
use Leochenftw\Restful\RestfulController;
use SilverStripe\Security\Member;
use Leochenftw\Debugger;
use KSolution\Dog;
use KSolution\Breed;
use App\Web\Model\BreedNotice;
use App\Web\Model\TradeNotice;

class DogAPI extends RestfulController
{
    private $member =   null;
    /**
     * Defines methods that can be called directly
     * @var array
     */
    private static $allowed_actions = [
        'delete'    =>  '->isAuthenticated',
        'get'       =>  true,
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
            // if ($set = $member->Photosets()->byID($request->Param('ID'))) {
            //     $set->delete();
            //     return '照片集已删除';
            // }
            //
            // return $this->httpError(404, '照片集不存在!');
        }

        return $this->httpError(403, '请先登录!');
    }

    public function get($request)
    {
        if ($this->member = Member::currentUser()) {
            $dogs   =   $this->member->Dogs();

            if ($id = $request->Param('ID')) {

                if ($dog = $dogs->byID($id)) {
                    return  $dog->getData();
                }

                return $this->httpError(404, '查无此犬');
            }

            return $dogs->getData();
        }

        return $this->httpError(403, '请先登录!');
    }

    public function post($request)
    {
        // return $request->postVars();
        if ($request->Param('Action') == 'delete_photo') {
            return $this->delete_photo($request);
        }

        if ($request->Param('Action') == 'post_breed_notice') {
            return $this->post_breed_notice($request);
        }

        if ($request->Param('Action') == 'post_trade_notice') {
            return $this->post_trade_notice($request);
        }

        if ($request->Param('Action') == 'withdraw_breed_notice') {
            return $this->withdraw_breed_notice($request);
        }

        if ($request->Param('Action') == 'withdraw_trade_notice') {
            return $this->withdraw_trade_notice($request);
        }

        if ($request->Param('Action') == 'delete') {
            return $this->delete($request);
        }

        if ($id = $request->Param('ID')) {
            $dog    =   Dog::get()->byID($id);
            if (!empty($dog)) {
                if ($photo = $request->postVar('photo')) {
                    return $dog->add_photo($photo, $request->postVar('idx'));
                }

                if (!empty($request->postVar('delete_dog_cert'))) {
                    if ($dog->CertCopy()->exists()) {
                        $dog->CertCopy()->deleteFromStage('Live');
                        $dog->CertCopy()->deleteFromStage('Stage');
                    }
                } elseif (!empty($request->postVar('dog_cert'))) {
                    if ($dog->CertCopy()->exists()) {
                        $dog->CertCopy()->deleteFromStage('Live');
                        $dog->CertCopy()->deleteFromStage('Stage');
                    }
                    $dog->CertCopyID    =   $dog->create_file($request->postVar('dog_cert')['tmp_name'], $request->postVar('dog_cert')['name']);
                    $dog->write();
                    return true;
                }

                if (!empty($request->postVar('delete_he_inspection'))) {
                    if ($dog->HEInpsection()->exists()) {
                        $dog->HEInpsection()->deleteFromStage('Live');
                        $dog->HEInpsection()->deleteFromStage('Stage');
                    }
                } elseif (!empty($request->postVar('he_inspection'))) {
                    if ($dog->HEInpsection()->exists()) {
                        $dog->HEInpsection()->deleteFromStage('Live');
                        $dog->HEInpsection()->deleteFromStage('Stage');
                    }
                    $dog->HEInpsectionID    =   $dog->create_file($request->postVar('he_inspection')['tmp_name'], $request->postVar('he_inspection')['name']);
                    $dog->write();
                    return true;
                }

            }
        }

        $dog    =   !empty($dog) ? $dog : Dog::create();

        $dog->Title         =   $this->sanitise($request->postVar('Title'));
        $dog->Sex           =   $this->sanitise($request->postVar('Sex'));
        $dog->DoB           =   $this->sanitise($request->postVar('DoB'));
        $dog->ChipsSerial   =   $this->sanitise($request->postVar('ChipsSerial'));
        $dog->Content       =   $this->sanitise($request->postVar('Content'));
        // $dog->CertNumber =   $this->sanitise($request->postVar('CertNumber'));
        $dog->JobTitle      =   $this->sanitise($request->postVar('JobTitle'));
        $dog->DNA           =   $this->sanitise($request->postVar('DNA'));
        // $dog->ReproTest =   $this->sanitise($request->postVar('ReproTest'));
        $dog->Breeder       =   $this->sanitise($request->postVar('Breeder'));
        $dog->OwnedBy       =   $this->sanitise($request->postVar('OwnedBy'));
        if ($breed = $this->sanitise($request->postVar('Breed'))) {
            $breed_object   =   Breed::get()->filter(['Title' => $breed])->first();
            if (empty($breed_object)) {
                $breed_object           =   Breed::create();
                $breed_object->Title    =   $breed;
                $breed_object->write();
            }
            $dog->BreedID   =   $breed_object->ID;
        }

        if ($dog_mum = $request->postVar('dog_mum')) {
            $dog_mum    =   json_decode($dog_mum);
            if (!empty($dog_mum->id)) {
                $id = $dog_mum->id;
                if ($id == $dog->ID) {
                    return $this->httpError(500, '不能添加自己为父母!');
                }
                $dog->MotherID  =   $id;
            } elseif ($serial = $dog_mum->serial) {
                if ($test_dog = Dog::get()->filter(['ChipsSerial' => $serial])->first()) {
                    if ($test_dog->ID == $dog->ID) {
                        return $this->httpError(500, '不能添加自己为父母!');
                    }
                }
                $new_dog                =   Dog::create();
                $new_dog->ChipsSerial   =   $serial;
                $new_dog->CurrentOwner  =   null;
                $new_dog->write();
                $dog->MotherID          =   $new_dog->ID;
            }
        }

        if ($dog_dad = $request->postVar('dog_dad')) {
            $dog_dad    =   json_decode($dog_dad);
            if (!empty($dog_dad->id)) {
                $id = $dog_dad->id;
                if ($id == $dog->ID) {
                    return $this->httpError(500, '不能添加自己为父母!');
                }
                $dog->FatherID  =   $id;
            } elseif ($serial = $dog_dad->serial) {
                if ($test_dog = Dog::get()->filter(['ChipsSerial' => $serial])->first()) {
                    if ($test_dog->ID == $dog->ID) {
                        return $this->httpError(500, '不能添加自己为父母!');
                    }
                }
                $new_dog                =   Dog::create();
                $new_dog->ChipsSerial   =   $serial;
                $new_dog->CurrentOwner  =   null;
                $new_dog->write();
                $dog->FatherID          =   $new_dog->ID;
            }
        }

        $dog->write();

        return $dog->getData();
    }

    private function post_breed_notice(&$request)
    {
        if ($id = $request->Param('ID')) {
            $dog    =   $this->member->Dogs()->byID($id);
            if (!empty($dog) && !$dog->BreedNotice()->exists()) {
                $notice =   BreedNotice::create();
                $notice->write();
                $dog->BreedNoticeID =   $notice->ID;
                return $dog->write();
            }
        }

        return $this->httpError(400, '未指定犬只');
    }

    private function post_trade_notice(&$request)
    {
        if ($id = $request->Param('ID')) {
            $dog    =   $this->member->Dogs()->byID($id);
            if (!empty($dog) && !$dog->TradeNotice()->exists()) {
                $notice =   TradeNotice::create();
                $notice->write();
                $dog->TradeNoticeID =   $notice->ID;
                return $dog->write();
            }
        }

        return $this->httpError(400, '未指定犬只');
    }

    private function withdraw_breed_notice(&$request)
    {
        if ($id = $request->Param('ID')) {
            $dog    =   $this->member->Dogs()->byID($id);
            if (!empty($dog) && $dog->BreedNotice()->exists()) {
                $dog->BreedNotice()->delete();
                return $dog->write();
            }
        }

        return $this->httpError(400, '未指定犬只');
    }

    private function withdraw_trade_notice(&$request)
    {
        if ($id = $request->Param('ID')) {
            $dog    =   $this->member->Dogs()->byID($id);
            if (!empty($dog) && $dog->TradeNotice()->exists()) {
                $dog->TradeNotice()->delete();
                return $dog->write();
            }
        }

        return $this->httpError(400, '未指定犬只');
    }

    private function delete_photo(&$request)
    {
        if ($id = $request->Param('ID')) {
            $dog    =   $this->member->Dogs()->byID($id);
            if (!empty($dog)) {
                if ($photo_id = $request->postVar('photo_id')) {
                    if ($photo = $dog->Photos()->byID($photo_id)) {
                        $dog->Photos()->remove($photo);
                        $photo->delete();
                        return true;
                    }
                }
            }
        }

        return $this->httpError(400, '未指定犬只');
    }

    private function sanitise($value)
    {
        if ($value == 'null') {
            return null;
        }

        return $value;
    }
}
