<?php

namespace RevStrat\BlogAnalytics;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;

class PageExtension extends TrafficExtension {
    private static $db = [
        'ExcludeFromTrafficCalculation' => 'Boolean'
    ];

    public function updateSettingsFields(FieldList $fields) {
        $fields->push(new CheckboxField('ExcludeFromTrafficCalculation', 'Exclude from traffic calculations'));
    }
}