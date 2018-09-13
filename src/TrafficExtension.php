<?php

namespace RevStrat\BlogAnalytics;
use SilverStripe\ORM\DataExtension;

class TrafficExtension extends DataExtension {
    public function getTrafficData() {
        return TrafficData::get()->filter([
            'ObjectID' => $this->owner->ID
        ])->first();
    }
}