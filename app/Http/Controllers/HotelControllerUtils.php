<?php
use Illuminate\Support\Facades\Http;

function getApiURL(){
    return 'https://hotels.cloudbeds.com/api/v1.1/';
}


function getReservations($checkinFrom, $checkinTo){
    $api_key = getApiToken();

    $head = array("Authorization" => 'Bearer ' . $api_key);
    $endpoint = getApiURL() . 'getReservations';
    $param = array('checkInFrom' => $checkinFrom, 'checkInTo' => $checkinTo);

    $response = Http::acceptJson()->withHeaders($head)->get($endpoint,$param);
    $response = json_decode($response);
    return "<pre>".json_encode($response,JSON_PRETTY_PRINT)."<pre\>";
    


}


function getApiToken(){
    $refresh_token = "LEDUzczTNnypxyPPkznxcTgwzSWZ1v7dM9uZ6nrTvD0";
    $client_id = "live1_215504_UvykDRiC70YNOdoM5TsnAhzP";
    $client_secret = "wGuaXND4vpdTmVcIWbMOSR7YFxr08J9q";

    $endpoint = getApiURL() . "access_token";
    $data = array("client_id" => $client_id, "client_secret" => $client_secret, "grant_type" => "refresh_token","refresh_token" => $refresh_token);
    $response = Http::acceptJson()->asForm()->post($endpoint,$data);

    return $response->json("access_token");
}

