<?php

namespace App\Http\Controllers;

use App\Models\Tracking;
use App\Models\Location;
use App\Models\Device;
use App\Models\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TrackingRequest;
use App\Http\Controllers\Controller;

class TrackingController extends Controller
{
    public function __construct()
    {
       $this->authorizeResource(Tracking::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Tracking $model)
    {
       // $this->authorize('manage-items', User::class);
        /*$trackings = $model::join('trackings', 'cluster_id', '=', 'Tags.id')
        ->get(['locations.*', 'tags.name as cluster_name', 'tags.color as cluster_color']);*/
        
        $data['colors'] = ['Mantenimiento'=>'#FD9E01','Activo'=>'#2BD129','Activo (SL)'=>'#11A3D2','Inactivo'=>'#CB0000'];
        $trackings = $model::get();

        foreach($trackings as $key => $track){
            $Logs = Log::latest()->take(1)
            ->Where('tracking_id', $track->id)
            ->Where('created_at', '>', Carbon::now()->subHour(1))
            ->orderBy('id','desc')
            ->get(['id','indoor_temperature', 'indoor_humidity','outdoor_temperature','outdoor_humidity','timestamp']);
            
            if (!$Logs->isEmpty()){
                $track->connection = "success";
                $track->lastRecord = "<p>I[".$Logs[0]->indoor_temperature."°C,".$Logs[0]->indoor_humidity."%]</p>"."<p>O[".$Logs[0]->outdoor_temperature."°C,".$Logs[0]->outdoor_humidity."%]</p>";
                $track->dateLastRecord = $Logs[0]->timestamp;
            }else{
                $track->connection = "danger";
            }
            $trackings[$key] = $track;
        }



        $data['tracking'] = $trackings;

        return view('tracking.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['locations'] = Location::get(["tag","id"]);
        $data['devices'] = Device::get(["label","id"]);        
        $data['colors'] = ['Mantenimiento'=>'#FD9E01','Activo'=>'#2BD129','Activo (SL)'=>'#11A3D2','Inactivo'=>'#CB0000'];
        return view('tracking.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TrackingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrackingRequest $request, Tracking $model)
    {

        $user = Auth::user(); 
        $success = $request->all();
        $success['api_token'] =  $user->createToken('EnergynoInternal')->plainTextToken; 
        $success['installation_date'] =  Carbon::parse(now())->format('Y-m-d h:i:s');
        $success['user_id'] =  $user->id;
        
        $model->create($success);

        return redirect()->route('tracking.index')->withStatus(__('Device Tracking successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tracking  $deviceTracking
     * @return \Illuminate\Http\Response
     */
    public function show(Tracking $tracking)
    {

        $location = Location::find($tracking->location_id);
        $device = Device::find($tracking->device_id);
        $data['code'] = $this->getInoCode($tracking->api_token,$location->ssid,$location->ssid_password,$tracking->id,$device->label);
        return view('tracking.show',$data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tracking  $deviceTracking
     * @return \Illuminate\Http\Response
     */
    public function edit(Tracking $tracking)
    {
        $data['locations'] = Location::get(["tag","id"]);
        $data['devices'] = Device::get(["label","id"]);        
        $data['tracking'] = $tracking;
        $data['colors'] = ['Mantenimiento'=>'#FD9E01','Activo'=>'#2BD129','Activo (SL)'=>'#11A3D2','Inactivo'=>'#CB0000'];
        return view('tracking.edit',$data);
    }

    /**
     *  the specified resource in storage.
     *
     * @param  \App\Http\Requests\TrackingRequest  $request
     * @param  \App\Models\Tracking  $deviceTracking
     * @return \Illuminate\Http\Response
     */
    public function update(TrackingRequest $request, Tracking $tracking)
    {
        $tracking->update($request->all());

        return redirect()->route('tracking.index')->withStatus(__('Tracking successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tracking  $tracking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tracking $tracking)
    {
        $tracking->delete();

        return redirect()->route('tracking.index')->withStatus(__('Tracking successfully deleted.'));
    }

    private function getInoCode($apiToken, $ssid, $ssid_password, $tracking_id,$device_label){
        $contents = Storage::disk('local')->get('energyno.ino');
        $code  = str_replace('***ssid***', $ssid, $contents);
        $code  = str_replace('***ssid_password***', $ssid_password, $code);
        $code  = str_replace('***api_token***', $apiToken, $code);
        $code  = str_replace('***deviceTackingid***', $tracking_id, $code);
        $code  = str_replace('***device_label***', $device_label, $code);
        return $code;
    }
}
