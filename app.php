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
    
    foreach($representative as $value){
        echo $value . "\n";
    }
}

/* Deleting previous data */

$listFeed = $worksheet->getListFeed();

// foreach ($listFeed->getEntries() as $entry) {
//     $entry->delete();
//     for($i=0; $i<1; $i++){
//         //$entry->delete();
//     }
    
// }


// echo "Data Delete Successfull\n";

/* Inserting data */

$time = '11:45';
$space = 109;

$listFeed = $worksheet->getListFeed();

for($i=0; $i < 2; $i++){
    $listFeed->insert([
        'time' => $time,
        'space' => $space,
    ]);

}
echo "Data Insert Successfull\n";

// $spreadsheetId = '1Acpl5fcEqKxxAg56CQLO5cHIXWp_1T-Dz00q2F-5R6g';
// $range = 'Sheet1!A1:D5';

// $values = [
//   'range' => "A1",
//   'majorDimension' => 'DIMENSION_UNSPECIFIED',
//   'values' => [
//     ["Item", "Cost", "Stocked", "Ship Date"],
//     ["Wheel", "$20.50", "4", "3/1/2016"],
//     ["Door", "$15", "2", "3/15/2016"],
//     ["Engine", "$100", "1", "30/20/2016"],
//   ],
// ];

// $body = new Google_Service_Sheets_ValueRange(array(
//   'values' => $values
// ));
// $params = array(
//   'valueInputOption' => 'RAW'
// );

// $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);