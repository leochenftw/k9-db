<?php

namespace KSolution\Controller;
use PageController;
use GuzzleHttp\Client;
use SaltedHerring\Debugger;

class SignupController extends PageController
{
    public function init()
    {
        parent::init();

        // $client     =   new Client(['base_uri' => 'https://api.nursingcouncil.org.nz/']);
        // $response   =   $client->request(
        //                     'GET',
        //                     'iqn/GetNERSOverseasID',
        //                     [
        //                         'query' =>  [
        //                                         'WebUserID' => $member_id
        //                                     ]
        //                     ]
        //                 );
        // return json_decode($response->getBody()->getContents());
    }
}
