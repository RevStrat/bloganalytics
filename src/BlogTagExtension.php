<?php

namespace RevStrat\BlogAnalytics;

use SilverStripe\TagField\TagField;
use SilverStripe\ORM\DataExtension;

class BlogTagExtension extends DataExtension {
    private static $db = [
        'LastPeriodTraffic' => 'Int',
        'TrafficUpdated' => 'Datetime'
    ];
}