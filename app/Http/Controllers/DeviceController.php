<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Http\Requests\DeviceRequest;


class DeviceController extends Controller
{
    public function __construct()
    {
       $this->authorizeResource(Device::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Device $model)
    {
        $this->authorize('manage-items', User::class);
        
        /*$user = Auth::user(); 
        $success['token'] =  $user->createToken('EnergynoInternal')->plainTextToken; 
        $success['name'] =  $user->name;*/

        $colors = ['StandBy'=>'#FD9E01','Operativo'=>'#2BD129','No Operativo'=>'#CB0000'];
        
        $devices = $model::get();
        return view('devices.index', ['devices' => $devices, 'colors'=>$colors]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('manage-items', User::class);
        $colors = ['StandBy'=>'#FD9E01','Operativo'=>'#2BD129','No Operativo'=>'#CB0000'];
        return view('devices.create',['colors'=>$colors]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\DeviceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DeviceRequest $request, Device $model)
    {
        $model->create($request->all());
        return redirect()->route('device.index')->withStatus(__('Device successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(Device $device)
    {
        $colors = ['StandBy'=>'#FD9E01','Operativo'=>'#2BD129','No Operativo'=>'#CB0000'];
        return view('devices.edit', ['device' => $device,'colors'=>$colors]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDevicesRequest  $request
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(DeviceRequest $request, Device $device)
    {
        $device->update($request->all());
        return redirect()->route('device.index')->withStatus(__('Device successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(Device $device)
    {
        $device->delete();

        return redirect()->route('device.index')->withStatus(__('device successfully deleted.'));
    }
}
