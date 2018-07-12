<?php

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

require __DIR__ . '/vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/client_secret.json');
$client = new Google_Client;
$client->useApplicationDefaultCredentials();

$client->setApplicationName("Something to do with my representatives");
$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);

if ($client->isAccessTokenExpired()) {
    $client->refreshTokenWithAssertion();
}

$accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
ServiceRequestFactory::setInstance(
    new DefaultServiceRequest($accessToken)
);


// Get our spreadsheet
$spreadsheet = (new Google\Spreadsheet\SpreadsheetService)
   ->getSpreadsheetFeed()
   ->getByTitle('WSDOT');
 
// Get the first worksheet (tab)
$worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
$worksheet = $worksheets[0];


$listFeed = $worksheet->getListFeed();

/** @var ListEntry */
foreach ($listFeed->getEntries() as $entry) {
    
    $representative = $entry->getValues();
    // echo $entry->getValues();
    foreach($representative as $value){
        echo $value;
    }
}

$listFeed = $worksheet->getListFeed();

$listFeed->insert([
    'one' => 'two'
]);