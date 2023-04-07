<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Exists;

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
    //Revisar la lógica para paginación.
    $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo, 'pageNumber' => 1);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);

    $response = json_decode($response);
    $dbHotels = array();
    
    $reservations = $response -> data;

    foreach($reservations  as $key => $reservation){
        
        //$dbHotels[$key]["reservations"] = $reservation;
        $dbHotels[$key] = array();
        $dbHotels[$key]['reservationID'] = $reservation -> reservationID;
        $dbHotels[$key]['status'] = $reservation -> status;
        $dbHotels[$key]['startDate'] = $reservation -> startDate;
        $dbHotels[$key]['endDate'] = $reservation -> endDate;
        $dbHotels[$key]['adults'] = $reservation -> adults;
        $dbHotels[$key]['children'] = $reservation -> children;
        $dbHotels[$key]['sourceName'] = $reservation -> sourceName;
        

        //$dbHotels[$key]["reservationsRates"] = getReservationsWithRateDetails($reservation->reservationID);
        /** iterar sobre los cuartos para revisar cada tarifa y el desglose con las fechas */

        $reservationsRates = getReservationsWithRateDetails($reservation->reservationID);
        
        foreach($reservationsRates as $datos){
            if(isset($datos -> sourceReservationID)){
                $dbHotels[$key]['sourceReservationID'] = $datos -> sourceReservationID;
            }
            //
    
            $dataRooms = $datos -> rooms;
            foreach($dataRooms as $rooms ){

                $dbHotels[$key]['roomID'] = $rooms -> roomID;       
                $dbHotels[$key]['roomType'] = "[" . $dbHotels[$key]['roomID'] . "] " . $rooms -> rateName;
                $detailedRoomRates = array();
                $detailedRoomRates = $rooms -> detailedRoomRates;
                
            }
        }

        /** json_decode puede servir para transformar los json de fechas y tarifas a un arreglo y posteriormente analizar los registros */
        
        //$dbHotels[$key]["reservationsInvoice"] = getReservationInvoiceInformation($reservation->reservationID);

        /** (reservationPayments) Iterar sobre los pagos para generar un texto concentrando los datos del pago  de la reservación */
        $reservationsInvoice = getReservationInvoiceInformation($reservation->reservationID);;
        $payments = $reservationsInvoice -> reservationPayments;
        $invoiceReservationRooms = $reservationsInvoice -> reservationRooms;
        $invoiceBalanceDetailed = $reservationsInvoice -> balanceDetailed;
        foreach($payments as $payment){ 
            $paymentType = $payment -> paymentType;
            $paymentDescription = $payment -> paymentDescription;
            $paymentDateTime = $payment -> paymentDateTime;
            $paymentAmount = $payment -> paymentAmount;
            $dbHotels[$key]['PaymentComments'] = "[" . $paymentType . "] " . $paymentDescription . ": " . $paymentDateTime . " - " . $paymentAmount . "\n";
        }
        foreach($invoiceReservationRooms as $reservationRooms){
            $dbHotels[$key]["nights"] = $reservationRooms -> nights;
            $dbHotels[$key]['subtotal'] = $reservationRooms -> roomTotal;
            $dbHotels[$key]['indexPriceNight'] = $dbHotels[$key]['subtotal'] / $dbHotels[$key]["nights"] ;
            $dbHotels[$key]['IVA'] = $dbHotels[$key]['subtotal'] * 0.16;
            $dbHotels[$key]['ISH'] = $dbHotels[$key]['subtotal'] * 0.03;
            $dbHotels[$key]['TotalTax'] = $dbHotels[$key]['subtotal'] * 0.19;
            $dbHotels[$key]['Total'] = $dbHotels[$key]['subtotal'] + $dbHotels[$key]['TotalTax'];
        }
        
        $dbHotels[$key]['extras'] = $invoiceBalanceDetailed -> additionalItems;
        $dbHotels[$key]['paid'] = $invoiceBalanceDetailed -> paid;
        $dbHotels[$key]['adjustments'] = $reservationsInvoice -> reservationAdjustmentsTotal;


        //$dbHotels[$key]["reservationsNotes"] = getNotes($reservation->reservationID);

        /* Iterar sobre las notas para generar un texto concentrando las notas en el registro*/

        $reservationsNotes = getNotes($reservation->reservationID);
        foreach ($reservationsNotes as $reservationsNote){
            $userName = $reservationsNote -> userName;
            $dateCreated = $reservationsNote -> dateCreated;
            $reservationNote = $reservationsNote -> reservationNote;
            $dbHotels[$key]['Notes'] = "[" . $userName . "] " . $dateCreated . ": " . $reservationNote . "\n" ;
        }
        $dbHotels[$key]['difference'] = $dbHotels[$key]['Total'] - $dbHotels[$key]['paid'];
        if($dbHotels[$key]['status'] = ("canceled" || "no_show")){
            $dbHotels_canceled[$key] = $dbHotels[$key];
        }
        $dbHotels_otherMonth = array();

        $tmpCheckIn = strtotime($dbHotels[$key]['startDate']);
        $tmpCheckOut = strtotime($dbHotels[$key]['endDate']);

        if($dbHotels[$key]['status'] = ("canceled" || "no_show") && date("m", $tmpCheckIn ) != date("m", $tmpCheckOut)){
            $dbHotels_otherMonth[$key] = $dbHotels[$key];
            $dbHotels_otherMonth[$key]["status"] = $dbHotels[$key]["status"];
            dd($dbHotels_otherMonth);
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