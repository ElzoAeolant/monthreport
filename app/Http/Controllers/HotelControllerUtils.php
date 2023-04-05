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
    #Revisar la lógica para paginación.
    $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo, 'pageNumber' => 2);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);

    $response = json_decode($response);
    $dbHotels = array();
    
    $reservations = $response -> data;

    foreach($reservations  as $key => $reservation){
        
        $dbHotels[$key]["reservations"] = $reservation;
        $dbHotels[$key]["reservationsRates"] = getReservationsWithRateDetails($reservation->reservationID);
        /** iterar sobre los cuartos para revisar cada tarifa y el desglose con las fechas */
        $reservationsRates = $dbHotels[$key]["reservationsRates"];
        foreach($reservationsRates as $datos){
            $dataRooms = $datos -> rooms;
            foreach($dataRooms as $rooms ){
                $detailedRoomRates = array();
                $detailedRoomRates = $rooms -> detailedRoomRates;
            }
        }

        /** json_decode puede servir para transformar los json de fechas y tarifas a un arreglo y posteriormente analizar los registros */
        
        $dbHotels[$key]["reservationsInvoice"] = getReservationInvoiceInformation($reservation->reservationID);
        /** (reservationPayments) Iterar sobre los pagos para generar un texto concentrando los datos del pago  de la reservación */
        $reservationsInvoice = $dbHotels[$key]["reservationsInvoice"];
        $payments = $reservationsInvoice -> reservationPayments;
        foreach($payments as $payment){ 
            $paymentType = $payment -> paymentType;
            $paymentDescription = $payment -> paymentDescription;
            $paymentDateTime = $payment -> paymentDateTime;
            $paymentAmount = $payment -> paymentAmount;
        }

        $dbHotels[$key]["reservationsNotes"] = getNotes($reservation->reservationID);
        /* Iterar sobre las notas para generar un texto concentrando las notas en el registro*/

        $reservationsNotes = $dbHotels[$key]["reservationsNotes"];
        foreach ($reservationsNotes as $reservationsNote){
            $userName = $reservationsNote -> userName;
            $dateCreated = $reservationsNote -> dateCreated;
            $reservationNote = $reservationsNote -> reservationNote;
        }
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