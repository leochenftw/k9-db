<?php

namespace App\Web\ModelAdmin;
use SilverStripe\Admin\ModelAdmin;
use App\Web\Model\Promotion;
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class PromotionDogAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS
     * @var array
     */
    private static $managed_models = [
        Promotion::class
    ];

    /**
     * URL Path for CMS
     * @var string
     */
    private static $url_segment = 'promitions';

    /**
     * Menu title for Left and Main CMS
     * @var string
     */
    private static $menu_title = '广告管理';


}
