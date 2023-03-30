<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Location;
use App\Models\Tracking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogsExport;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $firstRecord = null;
        $countRecords = 0;
        $sizeof = 0;
        $dateFirstRecord = "--";
        if (Log::first()){
            $firstRecord = Log::first()->toJson();
            $countRecords = Log::latest()->orderBy('id','desc')->first()->id;
            $sizeof = number_format(strlen($firstRecord) / 1048576 * $countRecords, 3);
            $dateFirstRecord = json_decode($firstRecord)->created_at;
        }
       
        $today = now();

        $records = Log::select(
            'tracking_id',
            DB::raw('count(*) as total'),
            DB::raw('AVG(indoor_temperature) as iTemp_avg'),
            DB::raw('AVG(outdoor_temperature) as oTemp_avg'),
            DB::raw('AVG(indoor_humidity) as iHum_avg'),
            DB::raw('AVG(outdoor_humidity) as oHum_avg'),
            DB::raw('MAX(timestamp) as last_hour')
        )
            ->groupBy('tracking_id')
            // ->orWhere('indoor_temperature','>',0)
            // ->orWhere('outdoor_temperature','>',0)
            // ->orWhere('indoor_humidity','>',0)
            // ->orWhere('outdoor_humidity','>',0)
            // ->where('indoor_temperature', '>', 0)
            // ->where('outdoor_temperature', '>', 0)
            // ->where('indoor_humidity', '>', 0)
            // ->where('outdoor_humidity', '>', 0)
            ->where('timestamp', '>=', Carbon::now()->subDays(5))
            ->get()->toArray();



        $records = Log::select(
            'tracking_id',
            DB::raw('count(*) as total'),
            DB::raw('AVG(indoor_temperature) as iTemp_avg'),
            DB::raw('AVG(outdoor_temperature) as oTemp_avg'),
            DB::raw('AVG(indoor_humidity) as iHum_avg'),
            DB::raw('AVG(outdoor_humidity) as oHum_avg'),
            DB::raw('MAX(timestamp) as last_hour')
        )
            ->groupBy('tracking_id')
            // ->where('indoor_temperature', '>', 0)
            // ->where('outdoor_temperature', '>', 0)
            // ->where('indoor_humidity', '>', 0)
            // ->where('outdoor_humidity', '>', 0)
            ->where('timestamp', '>=', Carbon::now()->subDays(7))
            ->get()->toArray();
        $label = "";
        $data = array();
        $data['logs'] = array();
        $data['th'] = array();
        $data["labels"] = null;


        foreach ($records as $record) {
            $location = Tracking::find($record["tracking_id"])->location()->get('id');
            $device = Tracking::find($record["tracking_id"])->device()->get('label')[0];
            $cluster = Location::find($location[0]->id)->cluster()->get(['id', 'color', 'name'])[0];
            $newLabel = /*$cluster->name . "_" .*/ $device->label . "(" . $record["last_hour"] . ")";
            $data["labels"][$cluster->id][] = $newLabel;
            $data['logs'][$cluster->id]["data"][$newLabel] = $record["total"];
            $data['logs'][$cluster->id]["label"] = $cluster->name;
            $data['logs'][$cluster->id]["borderColor"] = $cluster->color;
            $data['logs'][$cluster->id]["backgroundColor"] = $cluster->color;

            $data['th']["iTemp"]["data"][$newLabel] = $record["iTemp_avg"];
            $data['th']["oTemp"]["data"][$newLabel] = $record["oTemp_avg"];
            $data['th']["iHum"]["data"][$newLabel] = $record["iHum_avg"];
            $data['th']["oHum"]["data"][$newLabel] = $record["oHum_avg"];
        }
        if (sizeof($records) > 0) {

            $data['th']["iTemp"]["label"] = "Temperatura Interior";
            $data['th']["iTemp"]["borderColor"] = '#FFB826';
            $data['th']["iTemp"]["backgroundColor"] = '#FFB826';

            $data['th']["oTemp"]["label"] = "Temperatura Exterior";
            $data['th']["oTemp"]["borderColor"] = '#FF2400';
            $data['th']["oTemp"]["backgroundColor"] = '#FF2400';

            $data['th']["iHum"]["label"] = "Humedad Interior";
            $data['th']["iHum"]["borderColor"] = '#00FFE8';
            $data['th']["iHum"]["backgroundColor"] = '#00FFE8';

            $data['th']["oHum"]["label"] = "Humedad Exterior";
            $data['th']["oHum"]["borderColor"] = '#0060FF';
            $data['th']["oHum"]["backgroundColor"] = '#0060FF';

            $labels = array();
            foreach ($data["labels"] as $key => $label) {
                foreach ($label as $lbl) {
                    array_push($labels, $lbl);
                }
            }
            sort($labels);

            foreach ($data['logs'] as $key => $value) {
                foreach ($labels as $label) {
                    if (!isset($value["data"][$label])) {
                        $data['logs'][$key]["data"][$label] = 0;
                    }
                }
            }

            foreach ($data['th'] as $key => $value) {
                foreach ($labels as $label) {
                    if (!isset($value["data"][$label])) {
                        $data['th'][$key]["data"][$label] = 0;
                    }
                }
            }

            foreach ($data['logs'] as $key => $value) {
                ksort($data['logs'][$key]["data"]);
            }

            foreach ($data['th'] as $key => $value) {
                ksort($data['th'][$key]["data"]);
            }


            $lbl = implode('","', $labels);

            $data["labels"] = '"' . $lbl . '"';
        }
        // Top Panel
        $data["update_records"] = $today;
        $data["records"] = $countRecords;
        $data["update_sizeof"] = $dateFirstRecord;
        $data["sizeof"] = "~" . $sizeof . " MB";
        return view('pages.dashboard', $data);
    }

    public function export() 
    {
        return Excel::download(new LogsExport, 'logs.xlsx');
    }
}
