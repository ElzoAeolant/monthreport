<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

use function PHPUnit\Framework\returnSelf;

function getApiURL(){
    return 'https://hotels.cloudbeds.com/api/v1.1/';
}
function getRefreshToken(){
    return "LEDUzczTNnypxyPPkznxcTgwzSWZ1v7dM9uZ6nrTvD0";
}
function getClientId(){
    return "live1_215504_UvykDRiC70YNOdoM5TsnAhzP";
}
function getClientSecret(){
    return "wGuaXND4vpdTmVcIWbMOSR7YFxr08J9q";
}

function getReservations($checkinFrom, $checkinTo){
    $api_key = getApiToken();
    
    $head = array("Authorization" => 'Bearer ' . $api_key);
    $endpoint = getApiURL() . 'getReservations';
    $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);

    $response = json_decode($response);
    $dbHotels = array();
    
    $data = $response -> data;

    foreach($data  as $key => $reservation){
        
        $dbHotels[$key]["reservations"] = $reservation;
        $dbHotels[$key]["reservationsRates"] = getReservationsWithRateDetails($reservation->reservationID);
        $dbHotels[$key]["reservationsInvoice"] = getReservationInvoiceInformation($reservation->reservationID);
        $dbHotels[$key]["reservationsNotes"] = getNotes($reservation->reservationID);

    }
    
    return "<pre>".json_encode($dbHotels,JSON_PRETTY_PRINT)."<pre\>";
    
}

function getApiToken(){   

    $endpoint = getApiURL() . "access_token";
    $data = array("client_id" => getClientId(), "client_secret" => getClientSecret(), "grant_type" => "refresh_token","refresh_token" => getRefreshToken());
    $response = Http::acceptJson()->asForm()->post($endpoint,$data);

    return $response->json("access_token");
}

function getReservationsWithRateDetails($reservationID){
    $endpoint = getApiURL() . "getReservationsWithRateDetails";
    $head = array("Authorization" => 'Bearer ' . getApiToken());
    $param = array("reservationID" => $reservationID);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);
    $response = json_decode($response);
    return $response -> data;
}

function getReservationInvoiceInformation($reservationID){
    $endpoint = getApiURL() . "getReservationInvoiceInformation";
    $head = array("Authorization" => 'Bearer ' . getApiToken());
    $param = array("reservationID" => $reservationID);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);
    $response = json_decode($response);
    return $response -> data;
}

function getNotes($reservationID){
    $endpoint = getApiURL() . "getReservationNotes";
    $head = array("Authorization" => 'Bearer ' . getApiToken());
    $param = array("reservationID" => $reservationID);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);
    $response = json_decode($response);
    return $response -> data;
}