<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Exists;
use League\CommonMark\Node\Query\AndExpr;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use function PHPUnit\Framework\returnSelf;
use App\Http\Controllers\exportArray;


function getApiURL()
{
    return 'https://hotels.cloudbeds.com/api/v1.1/';
}
function getRefreshToken($hotel)
{
    if($hotel == "Jade")
    {
        $msg = "LEDUzczTNnypxyPPkznxcTgwzSWZ1v7dM9uZ6nrTvD0";
    }else if($hotel == "Ophelia"){
        $msg = "an7p3tveHk039zUXEP0MT93Z8DRomAb2v4AWeoyVGJk";
    }else if($hotel == "Atman"){
        $msg = "2fC6HgnYPsIq2aE7y4I84AVu0C0vDX8yQS_VFtJbals";
    }
    return $msg;
}
function getClientId($hotel)
{
    if($hotel == "Jade")
    {
        $msg = "live1_215504_UvykDRiC70YNOdoM5TsnAhzP";
    }else if($hotel == "Ophelia"){
        $msg = "live1_213637_BF5J9CNb3T0RunrKgf4Zzmi7";
    }else if($hotel == "Atman"){
        $msg = "live1_213640_wDjgICh9YSuykAE4vG7Jberm";
    }
    return $msg;
    
}
function getClientSecret($hotel)
{
    if($hotel == "Jade")
    {
        $msg = "wGuaXND4vpdTmVcIWbMOSR7YFxr08J9q";
    }else if($hotel == "Ophelia"){
        $msg = "fDQ1ykgV7nlzvamA2MrpOZwIKqTRXchE";
    }else if($hotel == "Atman"){
        $msg = "ou2TPIckDCVi45m3pUy7M8R6Wzsl9gEv";
    }
    return $msg;
}

function getReservations($checkinFrom, $checkinTo, $hotel)
{

    $api_key = getApiToken($hotel);
    $head = array("Authorization" => 'Bearer ' . $api_key);
    $endpoint = getApiURL() . 'getReservations';

    $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint, $param);
    $response = json_decode($response);
    
    if ($response->total > 100) {
        $pagination = $response->total / 100;
        $paginationRound = ceil($pagination);
    } else {
        $paginationRound = 1;
    }
    
    $dbReservations_otherMonth = array();
    $dbReservations_simples = array();
    $dbReservations_canceled = array();
    $dbReservation_outPool = array();
    $dbReservations = array();
    $hotelIndex = -1;

    for ($page = 1; $page <= $paginationRound; $page++) {
        
        $api_key = getApiToken($hotel);
        $head = array("Authorization" => 'Bearer ' . $api_key);
        $endpoint = getApiURL() . 'getReservations';
        $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo, 'pageNumber' => $page);
        $response = Http::acceptJson()->withHeaders($head)->get($endpoint, $param);
        $response = json_decode($response);

        $reservations[$page] = $response->data;
        
        foreach ($reservations[$page]  as $key => $reservation) {
            $hotelIndex = $hotelIndex+1;

            $dbReservations[$hotelIndex] = array();
            $dbReservations[$hotelIndex]['guestName'] = $reservation->guestName;
            $dbReservations[$hotelIndex]['reservationID'] = $reservation->reservationID;
            $dbReservations[$hotelIndex]['status'] = $reservation->status;
            $dbReservations[$hotelIndex]['startDate'] = $reservation->startDate;
            $dbReservations[$hotelIndex]['endDate'] = $reservation->endDate;
            $dbReservations[$hotelIndex]['adults'] = $reservation->adults;
            $dbReservations[$hotelIndex]['children'] = $reservation->children;
            $dbReservations[$hotelIndex]['sourceName'] = $reservation->sourceName;
            /* iterar sobre los cuartos para revisar cada tarifa y el desglose con las fechas */

            $reservationsRates = getReservationsWithRateDetails($reservation->reservationID, $hotel);

            foreach ($reservationsRates as $datos) {
                if (isset($datos->sourceReservationID)) {
                    $dbReservations[$hotelIndex]['sourceReservationID'] = $datos->sourceReservationID;
                }
                $dataRooms = $datos->rooms;
                $dbReservations[$hotelIndex]['rooms'] = array();
                foreach ($dataRooms as $roomIdx => $rooms) {
                    $dbReservations[$hotelIndex]['rooms'][$roomIdx] = array();
                    $dbReservations[$hotelIndex]['rooms'][$roomIdx]['roomID'] = $rooms->roomID;
                    $dbReservations[$hotelIndex]['rooms'][$roomIdx]['roomType'] = "[" . $rooms->roomID . "] " . $rooms->rateName;
                    $dbReservations[$hotelIndex]['rooms'][$roomIdx]['roomRates'] = $rooms->detailedRoomRates;
                }
            }
            
            /** json_decode puede servir para transformar los json de fechas y tarifas a un arreglo y posteriormente analizar los registros */


            /** (reservationPayments) Iterar sobre los pagos para generar un texto concentrando los datos del pago  de la reservación */
            $reservationsInvoice = getReservationInvoiceInformation($reservation->reservationID, $hotel);;
            $payments = $reservationsInvoice->reservationPayments;
            $invoiceReservationRooms = $reservationsInvoice->reservationRooms;
            $invoiceBalanceDetailed = $reservationsInvoice->balanceDetailed;
            $paymentComments = "";
            foreach ($payments as $payment) {
                $paymentType = $payment->paymentType;
                $paymentDescription = $payment->paymentDescription;
                $paymentDateTime = $payment->paymentDateTime;
                $paymentAmount = $payment->paymentAmount;
                $paymentComments = "[" . $paymentType . "] " . $paymentDescription . ": " . $paymentDateTime . " - " . $paymentAmount . "\n";
            }
            $dbReservations[$hotelIndex]['PaymentComments'] = $paymentComments;
            
            foreach ($invoiceReservationRooms as $reservationRooms) {
                $dbReservations[$hotelIndex]["nights"] = $reservationRooms->nights;
                $dbReservations[$hotelIndex]['subtotal'] = $reservationRooms->roomTotal;
                $dbReservations[$hotelIndex]['indexPriceNight'] = $dbReservations[$hotelIndex]['subtotal'] / $dbReservations[$hotelIndex]["nights"];
                $dbReservations[$hotelIndex]['IVA'] = $dbReservations[$hotelIndex]['subtotal'] * 0.16;
                $dbReservations[$hotelIndex]['ISH'] = $dbReservations[$hotelIndex]['subtotal'] * 0.03;
                $dbReservations[$hotelIndex]['TotalTax'] = $dbReservations[$hotelIndex]['subtotal'] * 0.19;
                $dbReservations[$hotelIndex]['Total'] = $dbReservations[$hotelIndex]['subtotal'] + $dbReservations[$hotelIndex]['TotalTax'];
            }

            $dbReservations[$hotelIndex]['extras'] = $invoiceBalanceDetailed->additionalItems;
            $dbReservations[$hotelIndex]['paid'] = $invoiceBalanceDetailed->paid;
            $dbReservations[$hotelIndex]['adjustments'] = $reservationsInvoice->reservationAdjustmentsTotal;


            /* Iterar sobre las notas para generar un texto concentrando las notas en el registro*/

            $reservationsNotes = getNotes($reservation->reservationID, $hotel);
            $notes= "";
            foreach ($reservationsNotes as $reservationsNote) {
                $userName = $reservationsNote->userName;
                $dateCreated = $reservationsNote->dateCreated;
                $reservationNote = $reservationsNote->reservationNote;
                $notes = "[" . $userName . "] " . $dateCreated . ": " . $reservationNote . "\n";
            }
            $dbReservations[$hotelIndex]['Notes'] = $notes;
            $dbReservations[$hotelIndex]['difference'] = $dbReservations[$hotelIndex]['Total'] - $dbReservations[$hotelIndex]['paid'];
        }
    }

    foreach ($dbReservations as $key => $reservation) {
        //Filtrar por status
        //Caso cancelados: Guardar solo en dbReservation_canceled          
        if ($reservation['status'] == "canceled" or $reservation['status'] == "no_show") {
            $dbReservations_canceled[$key] = $reservation;
            $dbReservations_canceled[$key]['MesAnterior'] = "NA";
            $dbReservations_canceled[$key]['flowCase'] = "NA";
            //TODO: Falta mandar a drive
        } else {
            // Caso no cancelado: 
            $tmpCheckIn = strtotime($dbReservations[$key]['startDate']);
            $tmpCheckOut = strtotime($dbReservations[$key]['endDate']);
            if (date("m", $tmpCheckIn) == date("m", $tmpCheckOut)) {
                //Reservaciones simples: Mismo mes
                foreach ($reservation["rooms"] as $room) {
                    $hotelIndex = $hotelIndex + 1;
                    $dbReservations_simples[$hotelIndex] = $reservation;
                    $initDate = $tmpCheckIn;
                    $endDate = $tmpCheckOut;
                    $sumRate = 0;
                    foreach ($room["roomRates"] as $date => $rate) {
                        $dateTmp = strtotime($date);
                        if (date("m", $initDate) == date("m", $dateTmp)) {
                            $sumRate = $sumRate + $rate;
                        }
                    }
                    $dbReservations_simples[$hotelIndex]['subtotal'] = $sumRate;
                    $dbReservations_simples[$hotelIndex]['nights'] = ($endDate - $initDate) / (60 * 60 * 24);
                    $dbReservations_simples[$hotelIndex]['indexPriceNight'] = $sumRate / $dbReservations_simples[$hotelIndex]['nights'];
                    $dbReservations_simples[$hotelIndex]['IVA'] = $sumRate * 0.16;
                    $dbReservations_simples[$hotelIndex]['ISH'] = $sumRate * 0.03;
                    $dbReservations_simples[$hotelIndex]['TotalTax'] = $sumRate * 0.19;
                    $dbReservations_simples[$hotelIndex]['Total'] = $sumRate + $dbReservations_simples[$hotelIndex]['TotalTax'];
                    $dbReservations_simples[$hotelIndex]['room'] = $room["roomType"];
                    $dbReservations_simples[$hotelIndex]['flowCase'] = "1";
                    $dbReservations_simples[$hotelIndex]['MesAnterior'] = "NO";
                    unset($dbReservations_simples[$hotelIndex]['rooms']);
                    //Out of pool
                    if (strpos($dbReservations_simples[$hotelIndex]['room'], "424789")) {
                        $dbReservation_outPool[$hotelIndex] = $dbReservations_simples[$hotelIndex];
                        $dbReservation_outPool[$hotelIndex]['flowCase'] = "5";
                        $dbReservation_outPool[$hotelIndex]['MesAnterior'] = "NO";
                        unset($dbReservations_simples[$hotelIndex]);
                    }
                }
            } else {
                //Reservaciones multiples meses.
                $formatDate = "d-m-Y";
                foreach ($reservation["rooms"] as $room) {
                    $hotelIndex = $hotelIndex + 1;
                    $initDate = $tmpCheckIn;
                    $endDate = $tmpCheckOut;
                    $sumRate = 0;
                    foreach ($room["roomRates"] as $date => $rate) {
                        $dateTmp = strtotime($date);
                        if (date("m", $initDate) == date("m", $dateTmp)) {
                            $sumRate = $sumRate + $rate;
                        } else {
                            $endDate = $dateTmp;
                            $dbReservations_otherMonth[$hotelIndex] = $reservation;
                            $dbReservations_otherMonth[$hotelIndex]['startDate'] = str(date($formatDate, $initDate));
                            $dbReservations_otherMonth[$hotelIndex]['endDate'] = str(date($formatDate, $endDate));
                            $dbReservations_otherMonth[$hotelIndex]['room'] = $room["roomType"];
                            $dbReservations_otherMonth[$hotelIndex]['subtotal'] = $sumRate;
                            $dbReservations_otherMonth[$hotelIndex]['nights'] = ($endDate - $initDate) / (60 * 60 * 24);
                            $dbReservations_otherMonth[$hotelIndex]['indexPriceNight'] = $sumRate / $dbReservations_otherMonth[$hotelIndex]['nights'];
                            $dbReservations_otherMonth[$hotelIndex]['IVA'] = $sumRate * 0.16;
                            $dbReservations_otherMonth[$hotelIndex]['ISH'] = $sumRate * 0.03;
                            $dbReservations_otherMonth[$hotelIndex]['TotalTax'] = $sumRate * 0.19;
                            $dbReservations_otherMonth[$hotelIndex]['Total'] = $sumRate + $dbReservations_otherMonth[$hotelIndex]['TotalTax'];
                            $dbReservations_otherMonth[$hotelIndex]['flowCase'] = "2";
                            $dbReservations_otherMonth[$hotelIndex]['MesAnterior'] = "X";
                            $initDate = $dateTmp;
                            $sumRate = 0;
                            $sumRate = $sumRate + $rate;
                        }
                    }
                    if (isset($dbReservations_otherMonth[$hotelIndex]['room'])) {
                        //Out of pool
                        if (strpos($dbReservations_otherMonth[$hotelIndex]['room'], "424789")) {
                            $dbReservation_outPool[$hotelIndex] = $dbReservations_otherMonth[$hotelIndex];
                            $dbReservation_outPool[$hotelIndex]['flowCase'] = "4";
                            $dbReservation_outPool[$hotelIndex]['MesAnterior'] = "SI";
                            unset($dbReservations_otherMonth[$hotelIndex]);
                        }
                    }
                    $hotelIndex = $hotelIndex + 1;
                    $endDate = $tmpCheckOut
                    $dbReservations_otherMonth[$hotelIndex] = $reservation;
                    $dbReservations_otherMonth[$hotelIndex]['startDate'] = date($formatDate, $initDate);
                    $dbReservations_otherMonth[$hotelIndex]['endDate'] = date($formatDate, $endDate);
                    $dbReservations_otherMonth[$hotelIndex]['room'] = $room["roomType"];
                    $dbReservations_otherMonth[$hotelIndex]['subtotal'] = $sumRate;
                    $dbReservations_otherMonth[$hotelIndex]['nights'] = ($endDate - $initDate) / (60 * 60 * 24);
                    $dbReservations_otherMonth[$hotelIndex]['indexPriceNight'] = $sumRate / $dbReservations_otherMonth[$hotelIndex]['nights'];
                    $dbReservations_otherMonth[$hotelIndex]['IVA'] = $sumRate * 0.16;
                    $dbReservations_otherMonth[$hotelIndex]['ISH'] = $sumRate * 0.03;
                    $dbReservations_otherMonth[$hotelIndex]['TotalTax'] = $sumRate * 0.19;
                    $dbReservations_otherMonth[$hotelIndex]['Total'] = $sumRate + $dbReservations_otherMonth[$hotelIndex]['TotalTax'];
                    $dbReservations_otherMonth[$hotelIndex]['flowCase'] = "3";
                    $dbReservations_otherMonth[$hotelIndex]['MesAnterior'] = "SI";
                    if (isset($dbReservations_otherMonth[$hotelIndex]['room'])) {
                        //Out of pool
                        if (strpos($dbReservations_otherMonth[$hotelIndex]['room'], "424789")) {
                            $dbReservation_outPool[$hotelIndex] = $dbReservations_otherMonth[$hotelIndex];
                            $dbReservation_outPool[$hotelIndex]['flowCase'] = "4";
                            $dbReservation_outPool[$hotelIndex]['MesAnterior'] = "SI";
                            unset($dbReservations_otherMonth[$hotelIndex]);
                        }
                    }
                }
            }
        }
    }

    $dbSortSimples = sortbyheader($dbReservations_simples);
    $dbSortOtherMonth = sortbyheader($dbReservations_otherMonth);
    $dbSortOutOfPool = sortbyheader($dbReservation_outPool);
    $dbSortCanceled = sortbyheader($dbReservations_canceled);
    
    return array_merge($dbSortSimples, $dbSortOtherMonth, $dbSortOutOfPool, $dbSortCanceled);
    
    
    //TODO: Agregar la sección de productos. 
    //return "<pre>" . json_encode(array_merge($dbReservation_outPool, $dbReservations_simples), JSON_PRETTY_PRINT) . "<pre\>";
}
function sortbyheader($reservations)
{
    $dbsortByHeader = array();
    foreach ($reservations as $key => $reservation) {
        $dbsortByHeader[$key] = array();
        $dbsortByHeader[$key]['ID'] = $reservation['reservationID'];

        if (isset($dbsortByHeader[$key]['Room_ID'])) {
            $dbsortByHeader[$key]['Room_ID'] = $reservation['room'];
        } else {
            $dbsortByHeader[$key]['Room_ID'] = "- - - - -";
        }

        $dbsortByHeader[$key]['Guest_Name'] = $reservation['guestName'];
        $dbsortByHeader[$key]['Fuente'] = $reservation['sourceName'];
        $dbsortByHeader[$key]['Source_ID'] = $reservation['reservationID'];
        $dbsortByHeader[$key]['CheckIn'] = $reservation['startDate'];
        $dbsortByHeader[$key]['CheckOut'] = $reservation['endDate'];
        $dbsortByHeader[$key]['Total_de_Noches'] = $reservation['nights'];
        $dbsortByHeader[$key]['Index precio por noche'] = $reservation['indexPriceNight'];
        $dbsortByHeader[$key]['Subtotal'] = $reservation['subtotal'];
        $dbsortByHeader[$key]['IVA'] = $reservation['IVA'];
        $dbsortByHeader[$key]['ISH'] = $reservation['ISH'];
        $dbsortByHeader[$key]['Total Tax'] = $reservation['TotalTax'];
        $dbsortByHeader[$key]['Total'] = $reservation['Total'];
        $dbsortByHeader[$key]['Extras'] = $reservation['extras'];
        $dbsortByHeader[$key]['Ajustes'] = $reservation['adjustments'];
        $dbsortByHeader[$key]['Pagado'] = $reservation['paid'];
        if (isset($reservation['PaymentComments'])) {
            $dbsortByHeader[$key]['ComentariosPago'] = $reservation['PaymentComments'];
        } else {
            $dbsortByHeader[$key]['ComentariosPago'] = "- - - - -";
        }

        $dbsortByHeader[$key]['Adults'] = $reservation['adults'];
        $dbsortByHeader[$key]['Childs'] = $reservation['children'];
        $dbsortByHeader[$key]['Status'] = $reservation['status'];
        $dbsortByHeader[$key]['Pago Total'] = $reservation['paid'];
        $dbsortByHeader[$key]['Diferencia'] = $reservation['difference'];

        if (isset($reservation['MesAnterior'])) {
            $dbsortByHeader[$key]['Mes Anterior'] = $reservation['MesAnterior'];
        } else {
            $dbsortByHeader[$key]['Mes Anterior'] = "- - - - -";
        }

        if (isset($reservation['Notes'])) {
            $dbsortByHeader[$key]['Comentarios'] = $reservation['Notes'];
        } else {
            $dbsortByHeader[$key]['Comentarios'] = "- - - - -";
        }

        if (isset($reservation['flowCase'])) {
            $dbsortByHeader[$key]['FlowCase'] = $reservation['flowCase'];
        } else {
            $dbsortByHeader[$key]['FlowCase'] = "- - - - -";
        }

        if (isset($reservation['room'])) {
            $dbsortByHeader[$key]['Room Type'] = $reservation['room'];
        } else {
            $dbsortByHeader[$key]['Room Type'] = "- - - - -";
        }
    }

    return $dbsortByHeader;
}

function getApiToken($hotel)
{

    $endpoint = getApiURL() . "access_token";
    $data = array("client_id" => getClientId($hotel), "client_secret" => getClientSecret($hotel), "grant_type" => "refresh_token", "refresh_token" => getRefreshToken($hotel));
    $response = Http::acceptJson()->asForm()->post($endpoint, $data);

    return $response->json("access_token");
}

function getReservationsWithRateDetails($reservationID, $hotel)
{
    $endpoint = getApiURL() . "getReservationsWithRateDetails";
    $head = array("Authorization" => 'Bearer ' . getApiToken($hotel));
    $param = array("reservationID" => $reservationID);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint, $param);
    $response = json_decode($response);
    return $response->data;
}

function getReservationInvoiceInformation($reservationID, $hotel)
{
    $endpoint = getApiURL() . "getReservationInvoiceInformation";
    $head = array("Authorization" => 'Bearer ' . getApiToken($hotel));
    $param = array("reservationID" => $reservationID);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint, $param);
    $response = json_decode($response);
    return $response->data;
}

function getNotes($reservationID, $hotel)
{
    $endpoint = getApiURL() . "getReservationNotes";
    $head = array("Authorization" => 'Bearer ' . getApiToken($hotel));
    $param = array("reservationID" => $reservationID);
    $response = Http::acceptJson()->withHeaders($head)->get($endpoint, $param);
    $response = json_decode($response);
    if (isset($response->data)) {
        return $response->data;
    } else {
        return null;
    }
}
