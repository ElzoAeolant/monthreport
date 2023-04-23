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
        array_push($headers,"Room_ID");
        array_push($headers,"Guest_Name");
        array_push($headers,"Fuente");
        array_push($headers,"Source_ID");
        array_push($headers,"Source_ID");
        array_push($headers,"Check_Out");
        array_push($headers,"Total_de_Noches");
        array_push($headers,"Index precio por noche");
        array_push($headers,"Subtotal");
        array_push($headers,"IVA");
        array_push($headers,"ISH");
        array_push($headers,"Total Tax");
        array_push($headers,"Total");
        array_push($headers,"Extras");
        array_push($headers,"Ajustes");
        array_push($headers,"Pagado");
        array_push($headers,"Comentarios");
        array_push($headers,"Adults");
        array_push($headers,"Childs");
        array_push($headers,"Status");
        array_push($headers,"Pago Total");
        array_push($headers,"Diferencia");
        array_push($headers,"Mes Anterior");
        array_push($headers,"Comentarios");
        array_push($headers,"FlowCase");
        array_push($headers,"Room Type");
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
