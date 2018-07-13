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


use  HtmlDom\HtmlDomParser;
/**
 * Created by PhpStorm.

 */

include_once ('libs/HttpHandler.php');
include_once ('libs/StringParser.php');
include_once ('HtmlDomParser/HtmlDomParser.php');

$httpHandler = new HttpHandler();
$link = "";

$source = $httpHandler->http_get($link, '');
$data = $source['FILE'];

$data = preg_replace('/new Date\(\d*\)/m', '""', $data);

$data = json_decode($data);

foreach($data->FeedContentList as $list){

    if($list->Terminal->TerminalID == 3){
        echo $list->Terminal->TerminalName."\n"; // Departure Terminal
        
        foreach($list->DepartSailingSpaces as $terminal){
            echo $terminal->Departure."\n"; // Departure Time
            echo $terminal->ArriveSailingSpaces[0]->TerminalName."\n"; // Destination Terminal
            echo $terminal->Vessel."\n";        
            echo $terminal->ArriveSailingSpaces[0]->DriveUpInfo."\n";
            // echo $terminal->ArriveSailingSpaces[0]->MaxSpaceCount."\n";

            $listFeed = $worksheet->getListFeed();

            $listFeed->insert([
                'terminal' => $list->Terminal->TerminalName,
                'time' => $terminal->Departure,
                'destination' => $terminal->ArriveSailingSpaces[0]->TerminalName,
                'spaces' =>  $terminal->ArriveSailingSpaces[0]->DriveUpInfo,
                'vessel' => $terminal->Vessel,
                'captured' => date('d-m-Y h:i')
            ]);

        }
    }
    echo "\n";
}


// $listFeed = $worksheet->getListFeed();

// /** @var ListEntry */
// foreach ($listFeed->getEntries() as $entry) {
    
//     $representative = $entry->getValues();
    
//     foreach($representative as $value){
//         echo $value . "\n";
//     }
// }

/* Deleting previous data */

// $listFeed = $worksheet->getListFeed();

// foreach ($listFeed->getEntries() as $entry) {
//     $entry->delete();
//     for($i=0; $i<1; $i++){
//         //$entry->delete();
//     }
    
// }

// echo "Data Delete Successfull\n";

/* Inserting data */

echo "Data Insert Successfull\n";
