<?php

use SilverStripe\Admin\ModelAdmin;
/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DogAdmin extends ModelAdmin
{
    /**
     * Managed data objects for CMS
     * @var array
     */
    private static $managed_models = [
        'KSolution\Dog',
        'KSolution\Breed'
    ];

    /**
     * URL Path for CMS
     * @var string
     */
    private static $url_segment = 'dogs';

    /**
     * Menu title for Left and Main CMS
     * @var string
     */
    private static $menu_title = '犬只与种类';


}
