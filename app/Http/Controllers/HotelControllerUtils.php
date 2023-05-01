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
    $spreadsheet = new Spreadsheet();

    $checkIns = $spreadsheet->getActiveSheet();

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
    $dbReservations = array();
    $dbReservations_otherMonth = array();
    $dbReservations_simples = array();
    $dbReservations_canceled = array();
    $dbReservation_outPool = array();


    for ($page = 1; $page <= $paginationRound; $page++) {
        $api_key = getApiToken($hotel);
        $head = array("Authorization" => 'Bearer ' . $api_key);
        $endpoint = getApiURL() . 'getReservations';
        $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo, 'pageNumber' => $page);
        $response = Http::acceptJson()->withHeaders($head)->get($endpoint, $param);
        $response = json_decode($response);

        $reservations[$page] = $response->data;

        foreach ($reservations[$page]  as $key => $reservation) {


            $dbReservations[$key] = array();
            $dbReservations[$key]['guestName'] = $reservation->guestName;
            $dbReservations[$key]['reservationID'] = $reservation->reservationID;
            $dbReservations[$key]['status'] = $reservation->status;
            $dbReservations[$key]['startDate'] = $reservation->startDate;
            $dbReservations[$key]['endDate'] = $reservation->endDate;
            $dbReservations[$key]['adults'] = $reservation->adults;
            $dbReservations[$key]['children'] = $reservation->children;
            $dbReservations[$key]['sourceName'] = $reservation->sourceName;
            /** iterar sobre los cuartos para revisar cada tarifa y el desglose con las fechas */

            $reservationsRates = getReservationsWithRateDetails($reservation->reservationID, $hotel);

            foreach ($reservationsRates as $datos) {
                if (isset($datos->sourceReservationID)) {
                    $dbReservations[$key]['sourceReservationID'] = $datos->sourceReservationID;
                }
                $dataRooms = $datos->rooms;
                $dbReservations[$key]['rooms'] = array();
                foreach ($dataRooms as $roomIdx => $rooms) {
                    $dbReservations[$key]['rooms'][$roomIdx] = array();
                    $dbReservations[$key]['rooms'][$roomIdx]['roomID'] = $rooms->roomID;
                    $dbReservations[$key]['rooms'][$roomIdx]['roomType'] = "[" . $rooms->roomID . "] " . $rooms->rateName;
                    $dbReservations[$key]['rooms'][$roomIdx]['roomRates'] = $rooms->detailedRoomRates;
                }
            }

            /** json_decode puede servir para transformar los json de fechas y tarifas a un arreglo y posteriormente analizar los registros */


            /** (reservationPayments) Iterar sobre los pagos para generar un texto concentrando los datos del pago  de la reservación */
            $reservationsInvoice = getReservationInvoiceInformation($reservation->reservationID, $hotel);;
            $payments = $reservationsInvoice->reservationPayments;
            $invoiceReservationRooms = $reservationsInvoice->reservationRooms;
            $invoiceBalanceDetailed = $reservationsInvoice->balanceDetailed;
            foreach ($payments as $payment) {
                $paymentType = $payment->paymentType;
                $paymentDescription = $payment->paymentDescription;
                $paymentDateTime = $payment->paymentDateTime;
                $paymentAmount = $payment->paymentAmount;
                $dbReservations[$key]['PaymentComments'] = "[" . $paymentType . "] " . $paymentDescription . ": " . $paymentDateTime . " - " . $paymentAmount . "\n";
            }
            foreach ($invoiceReservationRooms as $reservationRooms) {
                $dbReservations[$key]["nights"] = $reservationRooms->nights;
                $dbReservations[$key]['subtotal'] = $reservationRooms->roomTotal;
                $dbReservations[$key]['indexPriceNight'] = $dbReservations[$key]['subtotal'] / $dbReservations[$key]["nights"];
                $dbReservations[$key]['IVA'] = $dbReservations[$key]['subtotal'] * 0.16;
                $dbReservations[$key]['ISH'] = $dbReservations[$key]['subtotal'] * 0.03;
                $dbReservations[$key]['TotalTax'] = $dbReservations[$key]['subtotal'] * 0.19;
                $dbReservations[$key]['Total'] = $dbReservations[$key]['subtotal'] + $dbReservations[$key]['TotalTax'];
            }

            $dbReservations[$key]['extras'] = $invoiceBalanceDetailed->additionalItems;
            $dbReservations[$key]['paid'] = $invoiceBalanceDetailed->paid;
            $dbReservations[$key]['adjustments'] = $reservationsInvoice->reservationAdjustmentsTotal;


            /* Iterar sobre las notas para generar un texto concentrando las notas en el registro*/

            $reservationsNotes = getNotes($reservation->reservationID, $hotel);
            foreach ($reservationsNotes as $reservationsNote) {
                $userName = $reservationsNote->userName;
                $dateCreated = $reservationsNote->dateCreated;
                $reservationNote = $reservationsNote->reservationNote;
                $dbReservations[$key]['Notes'] = "[" . $userName . "] " . $dateCreated . ": " . $reservationNote . "\n";
            }
            $dbReservations[$key]['difference'] = $dbReservations[$key]['Total'] - $dbReservations[$key]['paid'];
        }

        foreach ($dbReservations as $key => $reservation) {
            //Filtrar por status
            //Caso cancelados: Guardar solo en dbReservation_canceled          
            if ($reservation['status'] == "canceled" or $reservation['status'] == "no_show") {
                $dbReservations_canceled[$key] = $reservation;
                foreach ($dbReservations_canceled[$key]["rooms"] as $room) {
                    $dbReservations_canceled[$key]['room'] = $room["roomType"];
                    $dbReservations_canceled[$key]['MesAnterior'] = "NA";
                    $dbReservations_canceled[$key]['flowCase'] = "NA";
                }
                //TODO: Falta mandar a drive
            } else {
                // Caso no cancelado: 
                $tmpCheckIn = strtotime($dbReservations[$key]['startDate']);
                $tmpCheckOut = strtotime($dbReservations[$key]['endDate']);
                if (date("m", $tmpCheckIn) == date("m", $tmpCheckOut)) {
                    //Reservaciones simples: Mismo mes
                    $dbReservations_simples[$key] = $reservation;
                    foreach ($dbReservations_simples[$key]["rooms"] as $room) {
                        $initDate = $tmpCheckIn;
                        $endDate = $tmpCheckOut;
                        $sumRate = 0;
                        foreach ($room["roomRates"] as $date => $rate) {
                            $dateTmp = strtotime($date);
                            if (date("m", $initDate) == date("m", $dateTmp)) {
                                $sumRate = $sumRate + $rate;
                                $dbReservations_simples[$key]['subtotal'] = $sumRate;
                                $dbReservations_simples[$key]['nights'] = ($endDate - $initDate) / (60 * 60 * 24);
                                $dbReservations_simples[$key]['indexPriceNight'] = $sumRate / $dbReservations_simples[$key]['nights'];
                                $dbReservations_simples[$key]['IVA'] = $sumRate * 0.16;
                                $dbReservations_simples[$key]['ISH'] = $sumRate * 0.03;
                                $dbReservations_simples[$key]['TotalTax'] = $sumRate * 0.19;
                                $dbReservations_simples[$key]['Total'] = $sumRate + $dbReservations_simples[$key]['TotalTax'];
                                $dbReservations_simples[$key]['room'] = $room["roomType"];
                                $dbReservations_simples[$key]['flowCase'] = "1";
                                $dbReservations_simples[$key]['MesAnterior'] = "NO";
                                unset($dbReservations_simples[$key]['rooms']);


                                // dd($dbReservations_simples);

                            }

                            //exportArray($dbReservations_simples);
                        }
                        //Out of pool
                        if (strpos($dbReservations_simples[$key]['room'], "424789")) {
                            $dbReservation_outPool[$key] = $dbReservations_simples[$key];
                            $dbReservation_outPool[$key]['flowCase'] = "5";
                            $dbReservation_outPool[$key]['MesAnterior'] = "NO";
                            unset($dbReservations_simples[$key]);
                        }
                    }
                } else {
                    //Reservaciones multiples meses.
                    $dbReservations_otherMonth[$key] = $reservation;
                    //dd($dbReservations_otherMonth[$key]["rooms"]);
                    $formatDate = "d-m-Y";
                    foreach ($dbReservations_otherMonth[$key]["rooms"] as $room) {
                        $initDate = $tmpCheckIn;
                        $endDate = $tmpCheckOut;
                        $sumRate = 0;
                        foreach ($room["roomRates"] as $date => $rate) {
                            $dateTmp = strtotime($date);
                            if (date("m", $initDate) == date("m", $dateTmp)) {
                                $sumRate = $sumRate + $rate;
                            } else {
                                $endDate = $dateTmp;
                                $dbReservations_otherMonth[$key] = $reservation;
                                $dbReservations_otherMonth[$key]['startDate'] = str(date($formatDate, $initDate));
                                $dbReservations_otherMonth[$key]['endDate'] = str(date($formatDate, $endDate));
                                $dbReservations_otherMonth[$key]['room'] = $room["roomType"];
                                $dbReservations_otherMonth[$key]['subtotal'] = $sumRate;
                                $dbReservations_otherMonth[$key]['nights'] = ($endDate - $initDate) / (60 * 60 * 24);
                                $dbReservations_otherMonth[$key]['indexPriceNight'] = $sumRate / $dbReservations_otherMonth[$key]['nights'];
                                $dbReservations_otherMonth[$key]['IVA'] = $sumRate * 0.16;
                                $dbReservations_otherMonth[$key]['ISH'] = $sumRate * 0.03;
                                $dbReservations_otherMonth[$key]['TotalTax'] = $sumRate * 0.19;
                                $dbReservations_otherMonth[$key]['Total'] = $sumRate + $dbReservations_otherMonth[$key]['TotalTax'];
                                $dbReservations_otherMonth[$key]['flowCase'] = "2";
                                $dbReservations_otherMonth[$key]['MesAnterior'] = "X";
                                $initDate = $dateTmp;
                                $sumRate = 0;
                                $sumRate = $sumRate + $rate;
                            }
                        }
                        if (isset($dbReservations_otherMonth[$key]['room'])) {
                            //Out of pool
                            if (strpos($dbReservations_otherMonth[$key]['room'], "424789")) {
                                $dbReservation_outPool[$key] = $dbReservations_otherMonth[$key];
                                $dbReservation_outPool[$key]['flowCase'] = "4";
                                $dbReservation_outPool[$key]['MesAnterior'] = "SI";
                                unset($dbReservations_otherMonth[$key]);
                            }
                        }

                        $endDate = $tmpCheckOut;
                        $dbReservations_otherMonth[$key + $response->total] = $reservation;
                        $dbReservations_otherMonth[$key + $response->total]['startDate'] = date($formatDate, $initDate);
                        $dbReservations_otherMonth[$key + $response->total]['endDate'] = date($formatDate, $endDate);
                        $dbReservations_otherMonth[$key + $response->total]['room'] = $room["roomType"];
                        $dbReservations_otherMonth[$key + $response->total]['subtotal'] = $sumRate;
                        $dbReservations_otherMonth[$key + $response->total]['nights'] = ($endDate - $initDate) / (60 * 60 * 24);
                        $dbReservations_otherMonth[$key + $response->total]['indexPriceNight'] = $sumRate / $dbReservations_otherMonth[$key + $response->total]['nights'];
                        $dbReservations_otherMonth[$key + $response->total]['IVA'] = $sumRate * 0.16;
                        $dbReservations_otherMonth[$key + $response->total]['ISH'] = $sumRate * 0.03;
                        $dbReservations_otherMonth[$key + $response->total]['TotalTax'] = $sumRate * 0.19;
                        $dbReservations_otherMonth[$key + $response->total]['Total'] = $sumRate + $dbReservations_otherMonth[$key + $response->total]['TotalTax'];
                        $dbReservations_otherMonth[$key + $response->total]['flowCase'] = "3";
                        $dbReservations_otherMonth[$key + $response->total]['MesAnterior'] = "SI";
                        if (isset($dbReservations_otherMonth[$key + $response->total]['room'])) {
                            //Out of pool
                            if (strpos($dbReservations_otherMonth[$key + $response->total]['room'], "424789")) {
                                $dbReservation_outPool[$key] = $dbReservations_otherMonth[$key + $response->total];
                                $dbReservation_outPool[$key]['flowCase'] = "4";
                                $dbReservation_outPool[$key]['MesAnterior'] = "SI";
                                unset($dbReservations_otherMonth[$key + $response->total]);
                            }
                        }
                    }
                }
            }
        }

        /*$reservations_simples = collect($dbReservations_simples);
    dd($reservations_simples);*/
        $dbSortSimples = sortbyheader($dbReservations_simples);
        $dbSortOtherMonth = sortbyheader($dbReservations_otherMonth);
        $dbSortOutOfPool = sortbyheader($dbReservation_outPool);
        $dbSortCanceled = sortbyheader($dbReservations_canceled);
    }
    return array_merge($dbSortSimples, $dbSortOtherMonth, $dbSortOutOfPool, $dbSortCanceled);
    //TODO: Agregar la sección de productos. 
    return "<pre>" . json_encode(array_merge($dbReservation_outPool, $dbReservations_simples), JSON_PRETTY_PRINT) . "<pre\>";
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
