<?php
namespace App\Exports;
ini_set('max_execution_time', 300);
use App\Models\Log;
use App\Models\Tracking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogsExport implements FromCollection,WithHeadings
{

    public function headings():array{
        $headers = array();
        //Headers
        array_push($headers,"ID");
        array_push($headers,"Ubicación");
        array_push($headers,"Cluster");
        array_push($headers,"Temperatura_Interior");
        array_push($headers,"Humedad_Interior");
        array_push($headers,"Temperatura_Exterior");
        array_push($headers,"Humedad_Exterior");
        array_push($headers,"Fecha_Hora");
        return $headers;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        set_time_limit(0);
        $logs = Log::all();	
	$newLog = array();
	foreach($logs as $key => $log){
		$tmpLog = array();
		$track = Tracking::find($log["tracking_id"]);
		$tmpLog ["ID"] = $log["id"];
		$tmpLog ["Ubicación"] = $track->location->tag;    
		$tmpLog ["Cluster"] = $track->location->cluster->name;    
		$tmpLog ["Temperatura_Interior"] = $log["indoor_temperature"];
		$tmpLog ["Humedad_Interior"] = $log["indoor_humidity"];
		$tmpLog ["Temperatura_Exterior"] = $log["outdoor_temperature"];
		$tmpLog ["Humedad_Exterior"] = $log["outdoor_humidity"];
		$tmpLog ["Fecha_Hora"] = $log["timestamp"];
		$newLog[$key] = $tmpLog;
		unset($tmpLog);
        }

	return collect($newLog);

        return Log::select(
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
            ->where('indoor_temperature', '>', 0)
            ->where('outdoor_temperature', '>', 0)
            ->where('indoor_humidity', '>', 0)
            ->where('outdoor_humidity', '>', 0)
            ->where('timestamp', '>=', Carbon::now()->subDays(5))
            ->get();
    }
}
