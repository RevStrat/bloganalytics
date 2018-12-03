<?php

namespace RevStrat\BlogAnalytics;
use SilverStripe\ORM\DataObject;

class TrafficData extends DataObject {
    private static $db = [
        'ObjectID' => 'Int',
        'LastPeriodTraffic' => 'Int',
        'ObjectClass' => 'Varchar(255)'
    ];
    private static $table_name = 'TrafficData';
}