<?php

namespace App\Web\Model;
use App\Web\Layout\DogPage;
use SilverStripe\ORM\DataObject;
use App\Web\Extensions\SortOrderExtension;
use Leochenftw\Util;

/**
 * Description
 *
 * @package silverstripe
 * @subpackage mysite
 */
class DogContent extends DataObject
{
    /**
     * Defines the database table name
     * @var string
     */
    private static $table_name = 'DogContent';

    /**
     * Database fields
     * @var array
     */
    private static $db = [
        'Title'     =>  'Varchar(129)',
        'Content'   =>  'HTMLText'
    ];

    /**
     * Defines summary fields commonly used in table columns
     * as a quick overview of the data for this dataobject
     * @var array
     */
    private static $summary_fields = [
        'Title'     =>  '标题',
        'getTeaser' =>  '内容'
    ];

    /**
     * Defines a default list of filters for the search context
     * @var array
     */
    private static $searchable_fields = [
        'Title',
        'Content'
    ];

    /**
     * Defines extension names and parameters to be applied
     * to this object upon construction.
     * @var array
     */
    private static $extensions = [
        SortOrderExtension::class
    ];

    /**
     * Has_one relationship
     * @var array
     */
    private static $has_one = [
        'DogPage'   =>  DogPage::class
    ];

    public function getData()
    {
        return [
            'title'     =>  $this->Title,
            'content'   =>  Util::preprocess_content($this->Content)
        ];
    }

    public function getTeaser()
    {
        return mb_substr(strip_tags(Util::preprocess_content($this->Content)), 0, 50) . '...';
    }
}
