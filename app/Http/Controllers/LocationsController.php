<?php

namespace App\Http\Controllers;

use App\Models\{Location,Tag};
use Illuminate\Http\Request;
use App\Models\{State,Municipality,Town};
use App\Http\Requests\LocationRequest;
class LocationsController extends Controller
{
    public function __construct()
    {
       $this->authorizeResource(Location::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Location $model)
    {
        $this->authorize('manage-items', User::class);
        $locations = $model::join('tags', 'cluster_id', '=', 'tags.id')
        ->get(['locations.*', 'tags.name as cluster_name', 'tags.color as cluster_color']);
        return view('locations.index', ['locations' => $locations]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['countries'] = State::get(["nombre","id"]);
        $data['clusters'] = Tag::get(["name","id"]);
        return view('locations.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationRequest $request, Location $model)
    {

        $model->create($request->all());

        return redirect()->route('location.index')->withStatus(__('Location successfully created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function edit(Location $location)
    {
        $data['location'] = $location;
        $data['countries'] = State::get(["nombre","id"]);
        $data['clusters'] = Tag::get(["name","id"]);
        return view('locations.edit',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, Location $location)
    {
        $location->update($request->all());
        return redirect()->route('location.index')->withStatus(__('Location successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location  $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('location.index')->withStatus(__('location successfully deleted.'));
    }

    public function getMunicipality(Request $request)
    {
        $data['states'] = Municipality::where("state_id",$request->country_id)
                    ->get(["nombre","id"]);
        return response()->json($data);
    }
    public function getTown(Request $request)
    {
        $data['cities'] = Town::where("municipality_id",$request->state_id)
                    ->get(["nombre","id"]);
        return response()->json($data);
    }
}
