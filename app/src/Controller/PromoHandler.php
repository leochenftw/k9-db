<?php

namespace KSolution\Controller;
use App\Web\Model\Promotion;
use App\Web\Model\PromoClick;
use PageController;
use SilverStripe\Control\HTTPRequest;
use Leochenftw\Debugger;
use SilverStripe\Control\Cookie;

class PromoHandler extends PageController
{
    public function index(HTTPRequest $request)
    {
        if ($hash = $request->param('hash')) {
            if ($promo = Promotion::get()->filter(['Hash' => $hash])->first()) {
                $cookie =   Cookie::get('k9db');
                if (!empty($cookie)) {
                    $click = PromoClick::get()->filter(['Cookie' => $cookie])->first();
                    if (empty($click)) {
                        $this->add_click($request, $promo);
                    } else {
                        $click->Contribution++;
                        $click->write();
                    }
                } else {
                    $this->add_click($request, $promo);
                }

                return $promo->Link()->exists() ? $this->redirect($promo->Link()->getLinkURL()) : $this->httpError(404);
            }
        }

        return $this->httpError(404);
    }

    private function add_click(&$request, &$promo)
    {
        $cookie         =   session_id();
        Cookie::set('k9db', $cookie, $expiry = 30);
        $click          =   PromoClick::create();
        $click->Title   =   $request->getIP();
        $click->Cookie  =   $cookie;
        $click->PromoID =   $promo->ID;
        $click->write();
    }

    public function Title()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        return '正在跳转...';
    }
}
