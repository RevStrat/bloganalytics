<?php

namespace RevStrat\BlogAnalytics;
use Page;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DataExtension;

class PageExtension extends DataExtension {
    private static $db = [
        'ExcludeFromTrafficCalculation' => 'Boolean'
    ];

    public function updateSettingsFields(FieldList $fields) {
        $fields->push(new CheckboxField('ExcludeFromTrafficCalculation', 'Exclude from traffic calculations'));
    }
}