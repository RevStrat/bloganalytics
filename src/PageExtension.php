<?php

namespace RevStrat\BlogAnalytics;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Blog\Model\BlogPost;
use SilverStripe\CMS\Model\SiteTree;

class PageExtension extends TrafficExtension {
    private static $db = [
        'ExcludeFromTrafficCalculation' => 'Boolean'
    ];

    public function updateSettingsFields(FieldList $fields) {
        $fields->push(new CheckboxField('ExcludeFromTrafficCalculation', 'Exclude from traffic calculations'));
    }

    public function getTopTags($limit = 6) {
        $results = $this->getTopByClass('BlogTag', $limit);
        $tagsAndTraffic = new ArrayList();
        foreach ($results as $item) {
            $tag = BlogTag::get()->byId($item['ID']);
            $tagsAndTraffic->add(new ArrayData([
                'Item' => $tag,
                'Traffic' => $item['LastPeriodTraffic']
            ]));
        }
        return $tagsAndTraffic;
    }

    public function getTopSiteTree($limit = 6) {
        $results = $this->getTopByClass('Page', $limit);
        $treeAndTraffic = new ArrayList();
        foreach ($results as $item) {
            $tag = SiteTree::get()->byId($item['ID']);
            $treeAndTraffic->add(new ArrayData([
                'Item' => $tag,
                'Traffic' => $item['LastPeriodTraffic']
            ]));
        }
        return $treeAndTraffic;
    }

    public function getTopBlogPosts($limit = 6) {
        $results = $this->getTopByClass('Page', $limit);
        $postsAndTraffic = new ArrayList();
        foreach ($results as $item) {
            $tag = BlogPost::get()->byId($item['ID']);
            $postsAndTraffic->add(new ArrayData([
                'Item' => $tag,
                'Traffic' => $item['LastPeriodTraffic']
            ]));
        }
        return $postsAndTraffic;
    }

    public function getTopByClass($class = "Page", $limit = 6) {
        $trafficData = TrafficData::get()->filter([
            'ObjectClass' => $class
        ])->sort('LastPeriodTraffic', 'DESC')->limit($limit);
        $result = [];
        foreach ($trafficData as $trafficItem) {
            $result[] = [
                'ID' => $trafficItem->ObjectID,
                'LastPeriodTraffic' => $trafficItem->LastPeriodTraffic
            ];
        }
        return $result;
    }
}