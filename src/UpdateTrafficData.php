<?php

namespace RevStrat\BlogAnalytics;
use Page;
use SilverStripe\Blog\Model\BlogTag;
use SilverStripe\Core\Environment;
use SilverStripe\ORM\FieldType\DBDatetime;
use Symbiote\QueuedJobs\Services\QueuedJob;
use Symbiote\QueuedJobs\Services\AbstractQueuedJob;
use Symbiote\QueuedJobs\Services\QueuedJobService;

class UpdateTrafficData extends AbstractQueuedJob {
    public function __construct() {
        $this->currentStep = 0;
        $this->totalSteps = 4;
    }

    public function getJobType() {
        return QueuedJob::QUEUED;
    }

    public function getTitle() {
        return 'Update traffic data';
    }

    public function process() {
        // Step 1: Pull Google Analytics
        $this->currentStep = 1;
        $client = GoogleAnalytics::initializeAnalytics();
        $reports = GoogleAnalytics::getReport($client);

        // Step 2: Process results into array of pages with traffic data
        $this->currentStep = 2;

        $report = $reports[0];
        $header = $report->getColumnHeader();
        $dimensionHeaders = $header->getDimensions();
        $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
        $rows = $report->getData()->getRows();

        $totalViews = 0;
        $pages = [];
        for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
            $row = $rows[ $rowIndex ];
            $dimensions = $row->getDimensions();
            $metrics = $row->getMetrics();
            $path = NULL;
            $currentViews = 0;
            $skip = false;
            // Get dimension (page path)
            for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                $sanitized = $url = preg_replace('/\?.*/', '', $dimensions[$i]);
                $sanitized = preg_replace('{/$}', '', $sanitized);
                $link_array = explode('/',$sanitized);
                $path = end($link_array);
            }
            // Get metric (page views)
            for ($j = 0; $j < count($metrics); $j++) {
                $values = $metrics[$j]->getValues();
                for ($k = 0; $k < count($values); $k++) {
                    $totalViews += $values[$k];
                    $currentViews = $values[$k];
                }
            }

            $minimumCutoff = Environment::getEnv('GAMINCUTOFF');
            if ($minimumCutoff && $currentViews < $minimumCutoff ) {
                break;
            }

            // Record path and associated page views
            $pages[] = [
                'path' => $path,
                'views' => $currentViews
            ];
        }
        // Step 3: Compute average traffic for each page and tag
        $this->currentStep = 3;
        $tags = [];
        foreach ($pages as $pageData) {
            $page = Page::get()->filter([
                'URLSegment' => $pageData['path']
            ])->first();
            if (!$page || $page->ExcludeFromTrafficCalculation) {
                continue;
            }
            $page->LastPeriodTraffic = $pageData['views'];
            $page->TrafficUpdated = DBDatetime::now()->getValue();
            $page->write();
            if (!$page->hasMethod('Tags')) {
                continue;
            }
            foreach ($page->Tags() as $tag) {
                if (array_key_exists($tag->ID, $tags)) {
                    $tags[$tag->ID] += $pageData['views'];
                } else {
                    $tags[$tag->ID] = $pageData['views'];
                }
            }
        }

        // Step 4: Write traffic deltas to Tags
        $this->currentStep = 4;
        foreach ($tags as $tagID => $tagData) {
            $tag = BlogTag::get()->byID($tagID);
            if ($tag) {
                $tag->LastPeriodTraffic = $tagData;
                $tag->TrafficUpdated = DBDatetime::now()->getValue();
                $tag->write();
            }
        }

        $this->isComplete = true;

        $nextQueuedJob = new UpdateTrafficData();
        singleton(QueuedJobService::class)
            ->queueJob($nextQueuedJob, date("Y-m-d H:i:s", strtotime('+3 hours'))); // Four times per day

        return;
    }
}
