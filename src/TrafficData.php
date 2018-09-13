<?php

namespace RevStrat\BlogAnalytics;
use SilverStripe\ORM\DataObject;

class TrafficData extends DataObject {
    private static $db = [
        'ObjectID' => 'Int',
        'LastPeriodTraffic' => 'Int'
    ];
    private static $table_name = 'TrafficData';
}