<?php

namespace RevStrat\BlogAnalytics;
use SilverStripe\Core\Environment;
use Silverstripe\SiteConfig\SiteConfig;

class GoogleAnalytics {
    public static function initializeAnalytics() {
        $config = json_decode( 
            preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode(Environment::getEnv('GASERVICE'))), 
            true
        );

        // Create and configure a new client object.
        $client = new \Google_Client();
        $client->setApplicationName("Civilized Analytics Reporting");
        $client->setAuthConfig($config);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $analytics = new \Google_Service_AnalyticsReporting($client);

        return $analytics;
    }

    public static function getReport($analytics) {
        // Replace with your view ID, for example XXXX.
        $VIEW_ID = Environment::getEnv('GAVIEW');
        $startDate = Environment::getEnv('GASTART');
        $endDate = Environment::getEnv('GAEND');

        // Create the DateRange object.
        $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($startDate ? $startDate : '7daysago');
        $dateRange->setEndDate($endDate ? $endDate : 'today');

        // Create the Metrics object.
        $sessions = new \Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression('ga:pageviews');
        $sessions->setAlias('pageviews');

        // Add a dimension
        $dimension = new \Google_Service_AnalyticsReporting_Dimension();
        $dimension->setName('ga:pagePath');

        // Set order
        $ordering = new \Google_Service_AnalyticsReporting_OrderBy();
        $ordering->setFieldName("ga:pageviews");
        $ordering->setOrderType("VALUE");   
        $ordering->setSortOrder("DESCENDING");

        // Create the ReportRequest object.
        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($sessions));
        $request->setDimensions(array($dimension));
        $request->setOrderBys($ordering);

        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests( array( $request) );
        return $analytics->reports->batchGet( $body );
    }
}