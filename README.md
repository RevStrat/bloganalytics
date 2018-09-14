# SilverStripe Site Analytics

Pull data out of Google Analytics for pages and blog tags

## Requirements

* SilverStripe ^4
* [SilverStripe Blog ^3.2 (for tag traffic)] (https://github.com/silverstripe/silverstripe-blog)
* [SilverStripe Queued Jobs ^4.2](https://github.com/symbiote/silverstripe-queuedjobs)

## Documentation

The SilverStripe BlogAnalytics module uses a Google Service Account to interact with Google Analytics.

1. Set up a [service account here](https://console.developers.google.com/iam-admin/serviceaccounts). You will receive a configuration such as this:
<pre>
{
  "type": "service_account",
  "project_id": "\<YOUR GOOGLE API PROJET ID>",
  "private_key_id": "\<YOUR PRIVATE KEY ID>",
  "private_key": "\<YOUR PRIVATE KEY>",
  "client_email": "\<EMAIL ADDRESS FOR THE SERVICE ACCOUNT>",
  "client_id": "\<CLIENT ID>",
  "auth_uri": "\<AUTH URI>",
  "token_uri": "\<TOKEN URI>",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "\<CERT URL>"
}
</pre>
2. Add the email address for the account to Google Analytics
3. Get a View ID for the Analytics view you wish to pull from
4. base64 encode the configuration
5. Add configuration to your environment:
<pre>
GASERVICE=""
GAVIEW="VIEW ID"
GASTART="7daysago"
GAEND="today"
GAMINCUTOFF="100" # Don't track pages with fewer views than this
</pre>
6. Install with `composer require revstrat\bloganalytics @dev`
7. Run `/dev/build?flush=all`.
8. Create a queued job of type RevStrat\BlogAnalytics\UpdateTrafficData and run it. The task will schedule itself on completion for 3 hours in the future
