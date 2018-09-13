<?php

namespace RevStrat\BlogAnalytics;

use SilverStripe\TagField\TagField;
use SilverStripe\ORM\DataExtension;

class TrafficExtension extends DataExtension {
    private static $db = [
        'LastPeriodTraffic' => 'Int',
        'TrafficUpdated' => 'Datetime'
    ];
}