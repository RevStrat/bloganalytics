<?php

namespace RevStrat\BlogAnalytics;

use SilverStripe\TagField\TagField;
use SilverStripe\ORM\DataExtension;

class BlogPostExtension extends DataExtension {
    private static $db = [
        'ExcludeFromTagTraffic' => 'Boolean'
    ];
}