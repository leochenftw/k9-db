<?php

namespace App\Web\Layout;
use PageController;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class PersonnelPageController extends PageController
{
    private static $url_handlers = [
        '$ID'   =>  'index'
    ];
}
