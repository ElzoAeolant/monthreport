<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Exists;
use League\CommonMark\Node\Query\AndExpr;

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
    //TODO: Trabajar en paginación de consultas.
    $head = array("Authorization" => 'Bearer ' . $api_key);
    $endpoint = getApiURL() . 'getReservations';
    
    $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);
    $response = json_decode($response);
    
    if($response->total > 100){
        $pagination = $response->total / 100;
        $paginationRound = ceil($pagination);
    }else{
        $paginationRound = 1;
    }
    for($page = 1; $page <= $paginationRound ;  $page++){
        $api_key = getApiToken();
        $head = array("Authorization" => 'Bearer ' . $api_key);
        $endpoint = getApiURL() . 'getReservations';
        $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo, 'pageNumber' => $page);
        $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);
        $response = json_decode($response);        
    }

        $dbReservations = array();  
        $dbReservations_otherMonth = array();
        $dbReservations_simples = array();
        $dbReservations_canceled = array();
        $reservations = $response -> data;
        dd($reservations);

        foreach($reservations  as $key => $reservation){
            
            //$dbReservations[$key]["reservations"] = $reservation;
            $dbReservations[$key] = array();
            $dbReservations[$key]['reservationID'] = $reservation -> reservationID;
            $dbReservations[$key]['status'] = $reservation -> status;
            $dbReservations[$key]['startDate'] = $reservation -> startDate;
            $dbReservations[$key]['endDate'] = $reservation -> endDate;
            $dbReservations[$key]['adults'] = $reservation -> adults;
            $dbReservations[$key]['children'] = $reservation -> children;
            $dbReservations[$key]['sourceName'] = $reservation -> sourceName;
            

            //$dbReservations[$key]["reservationsRates"] = getReservationsWithRateDetails($reservation->reservationID);
            /** iterar sobre los cuartos para revisar cada tarifa y el desglose con las fechas */

            $reservationsRates = getReservationsWithRateDetails($reservation->reservationID);
            
            foreach($reservationsRates as $datos){
                if(isset($datos -> sourceReservationID)){
                    $dbReservations[$key]['sourceReservationID'] = $datos -> sourceReservationID;
                }
                $dataRooms = $datos -> rooms;
                $dbReservations[$key]['rooms']=array();
                foreach($dataRooms as $roomIdx => $rooms ){
                    $dbReservations[$key]['rooms'][$roomIdx] = array();
                    $dbReservations[$key]['rooms'][$roomIdx]['roomID'] = $rooms -> roomID;       
                    $dbReservations[$key]['rooms'][$roomIdx]['roomType'] = "[" . $rooms -> roomID . "] " . $rooms -> rateName;
                    $dbReservations[$key]['rooms'][$roomIdx]['roomRates'] = $rooms -> detailedRoomRates;
                }
            }

            /** json_decode puede servir para transformar los json de fechas y tarifas a un arreglo y posteriormente analizar los registros */
            
            //$dbReservations[$key]["reservationsInvoice"] = getReservationInvoiceInformation($reservation->reservationID);

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
                $dbReservations[$key]['PaymentComments'] = "[" . $paymentType . "] " . $paymentDescription . ": " . $paymentDateTime . " - " . $paymentAmount . "\n";
            }
            foreach($invoiceReservationRooms as $reservationRooms){
                $dbReservations[$key]["nights"] = $reservationRooms -> nights;
                $dbReservations[$key]['subtotal'] = $reservationRooms -> roomTotal;
                $dbReservations[$key]['indexPriceNight'] = $dbReservations[$key]['subtotal'] / $dbReservations[$key]["nights"] ;
                $dbReservations[$key]['IVA'] = $dbReservations[$key]['subtotal'] * 0.16;
                $dbReservations[$key]['ISH'] = $dbReservations[$key]['subtotal'] * 0.03;
                $dbReservations[$key]['TotalTax'] = $dbReservations[$key]['subtotal'] * 0.19;
                $dbReservations[$key]['Total'] = $dbReservations[$key]['subtotal'] + $dbReservations[$key]['TotalTax'];
            }
            
            $dbReservations[$key]['extras'] = $invoiceBalanceDetailed -> additionalItems;
            $dbReservations[$key]['paid'] = $invoiceBalanceDetailed -> paid;
            $dbReservations[$key]['adjustments'] = $reservationsInvoice -> reservationAdjustmentsTotal;


            //$dbReservations[$key]["reservationsNotes"] = getNotes($reservation->reservationID);

            /* Iterar sobre las notas para generar un texto concentrando las notas en el registro*/

            $reservationsNotes = getNotes($reservation->reservationID);
            foreach ($reservationsNotes as $reservationsNote){
                $userName = $reservationsNote -> userName;
                $dateCreated = $reservationsNote -> dateCreated;
                $reservationNote = $reservationsNote -> reservationNote;
                $dbReservations[$key]['Notes'] = "[" . $userName . "] " . $dateCreated . ": " . $reservationNote . "\n" ;
            }
            $dbReservations[$key]['difference'] = $dbReservations[$key]['Total'] - $dbReservations[$key]['paid'];
        
        }
        
        foreach($dbReservations as $key => $reservation ){
            //Filtrar por status
            //Caso cancelados: Guardar solo en dbReservation_canceled
            if($reservation['status'] == "canceled" || $reservation['status'] == "no_show"){
                $dbReservations_canceled[$key] = $reservation;
                //TODO: Falta mandar a drive
            }else{
                // Caso no cancelado: 
                $tmpCheckIn = strtotime($dbReservations[$key]['startDate']);
                $tmpCheckOut = strtotime($dbReservations[$key]['endDate']);
        
                    if(date("m", $tmpCheckIn ) == date("m", $tmpCheckOut)){
                        //Reservaciones simples: Mismo mes
                        $dbReservations_simples[$key] = $reservation;
                        foreach ($dbReservations_simples[$key]["rooms"] as $room){
                            $initDate = $tmpCheckIn;
                            $endDate = $tmpCheckOut;
                            $sumRate = 0;
                            foreach($room["roomRates"] as $date => $rate){
                                $dateTmp = strtotime($date);
                                if(date("m", $initDate ) == date("m", $dateTmp)){
                                    $sumRate = $sumRate + $rate;
                                    $dbReservations_simples[$key]['subtotal'] = $sumRate;
                                    $dbReservations_simples[$key]['nights'] = ($endDate - $initDate) / (60*60*24);
                                    $dbReservations_simples[$key]['indexPriceNight'] = $sumRate / $dbReservations_simples[$key]['nights'] ;
                                    $dbReservations_simples[$key]['IVA'] = $sumRate * 0.16;
                                    $dbReservations_simples[$key]['ISH'] = $sumRate * 0.03;
                                    $dbReservations_simples[$key]['TotalTax'] = $sumRate * 0.19;
                                    $dbReservations_simples[$key]['Total'] = $sumRate + $dbReservations_simples[$key]['TotalTax'];
                                    $dbReservations_simples[$key]['rooms'] = $room["roomType"] ; 
                                }
                        }}
                    }else{
                        //Reservaciones multiples meses.
                        $dbReservations_otherMonth[$key] = $reservation;
                        //dd($dbReservations_otherMonth[$key]["rooms"]);
                        $formatDate = "d-m-Y";
                        foreach ($dbReservations_otherMonth[$key]["rooms"] as $room){
                            $initDate = $tmpCheckIn;
                            $endDate = $tmpCheckOut;
                            $sumRate = 0;
                            foreach($room["roomRates"] as $date => $rate){
                                $dateTmp = strtotime($date);
                                if(date("m", $initDate ) == date("m", $dateTmp)){
                                    $sumRate = $sumRate + $rate;
                                }else{
                                    $endDate = $dateTmp;
                                    //echo ($room["roomID"] . ": " . date($formatDate,$initDate) . "-" . date($formatDate,$endDate) . ":" . $sumRate . "<br>");
                                    $dbReservations_otherMonth[$key] = $reservation;
                                    $dbReservations_otherMonth[$key]['startDate'] = str(date($formatDate,$initDate));
                                    $dbReservations_otherMonth[$key]['endDate'] = str(date($formatDate,$endDate));
                                    $dbReservations_otherMonth[$key]['rooms'] = $room["roomType"] ;   
                                    $dbReservations_otherMonth[$key]['subtotal'] = $sumRate;
                                    $dbReservations_otherMonth[$key]['nights'] = ($endDate - $initDate) / (60*60*24);
                                    $dbReservations_otherMonth[$key]['indexPriceNight'] = $sumRate / $dbReservations_otherMonth[$key]['nights'] ;
                                    $dbReservations_otherMonth[$key]['IVA'] = $sumRate * 0.16;
                                    $dbReservations_otherMonth[$key]['ISH'] = $sumRate * 0.03;
                                    $dbReservations_otherMonth[$key]['TotalTax'] = $sumRate * 0.19;
                                    $dbReservations_otherMonth[$key]['Total'] = $sumRate + $dbReservations_otherMonth[$key]['TotalTax'];
                                    $initDate = $dateTmp;
                                    $sumRate = 0;
                                    $sumRate = $sumRate + $rate;
                                }
                            }
                            $endDate = $tmpCheckOut;
                            //echo (date($formatDate,$initDate) . "-" . date($formatDate,$endDate) . ":" . $sumRate . "<br>");
                            $dbReservations_otherMonth[$key+count($dbReservations)] = $reservation;
                            $dbReservations_otherMonth[$key+count($dbReservations)]['startDate'] = date($formatDate,$initDate);
                            $dbReservations_otherMonth[$key+count($dbReservations)]['endDate'] = date($formatDate,$endDate);
                            $dbReservations_otherMonth[$key+count($dbReservations)]['rooms'] = $room["roomType"] ; 
                            $dbReservations_otherMonth[$key+count($dbReservations)]['subtotal'] = $sumRate;
                            $dbReservations_otherMonth[$key+count($dbReservations)]['nights'] = ($endDate - $initDate) / (60*60*24);
                            $dbReservations_otherMonth[$key+count($dbReservations)]['indexPriceNight'] = $sumRate / $dbReservations_otherMonth[$key]['nights'] ;
                            $dbReservations_otherMonth[$key+count($dbReservations)]['IVA'] = $sumRate * 0.16;
                            $dbReservations_otherMonth[$key+count($dbReservations)]['ISH'] = $sumRate * 0.03;
                            $dbReservations_otherMonth[$key+count($dbReservations)]['TotalTax'] = $sumRate * 0.19;
                            $dbReservations_otherMonth[$key+count($dbReservations)]['Total'] = $sumRate + $dbReservations_otherMonth[$key]['TotalTax'];
                        
                    }
                }
        }
    }
   

    return "<pre>".json_encode($dbReservations,JSON_PRETTY_PRINT)."<pre\>";
    
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
    if(isset($response -> data)){
        return $response -> data;
    }else{
        return null;
    }
}