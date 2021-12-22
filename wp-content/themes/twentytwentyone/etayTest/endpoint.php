<?php

getApi();
/*function getApi(){
    echo 'etay';
    $player1 = isset($_GET['player1']) ? $_GET['player1'] : '';
    $player2 = isset($_GET['player2']) ? $_GET['player2'] : '';
}*/

function getApi(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://84.95.247.169:3003',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('id' => '1673890','text' => 'חמה על מיליון אזרחים חרדים". לאור ההתקדמות במגעים להקמת ממשלה חדשה, בנט, לפיד, סער וליברמן יגיעו היום ','voice' => 'Osnat','created' => '1622544829','updated' => '1638875657'),
        CURLOPT_HTTPHEADER => array(
            'X-RapidAPI-Host: ',
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}
