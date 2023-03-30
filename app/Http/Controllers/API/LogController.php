<?php

namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Log;
use App\Models\Tracking;


use App\Http\Resources\LogResource;
use App\Models\Device;

class LogController extends BaseController
{
     /**
     * @OA\Get(
     *      path="/log",
     *      operationId="getProjectsList",
     *      tags={"Projects"},
     *      summary="Get list of projects",
     *      description="Returns list of projects",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index()
    {
        $Logs = Log::latest()->take(5)
                //->orWhere('indoor_temperature','>',0)
                ->orderBy('id','desc')
                ->get();
        return $this->sendResponse(LogResource::collection($Logs), 'Logs retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'tracking_id' => 'required',
            'indoor_temperature' => 'required',
            'indoor_humidity' => 'required',
            'outdoor_temperature' => 'required',
            'outdoor_humidity' => 'required'
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $tracking = Tracking::find($request->input('tracking_id'));
        
        if ($tracking){
            if( $tracking->device()->first()->status == "Operativo" && ($tracking->status == "Activo" || $tracking->status == "Activo (SL)" )){
                $product = Log::create($input);    
                return $this->sendResponse(new LogResource($product), 'Tracking log saved successfully.');
            }else{
                return $this->sendResponse(new LogResource($input), 'Tracking log ommited successfully.');
            }
            
        }else{
             return $this->sendError('Tracking fails.');
        }
    }
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Log::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return $this->sendResponse(new LogResource($product), 'Product retrieved successfully.');
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Log $product)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->save();
   
        return $this->sendResponse(new LogResource($product), 'Product updated successfully.');
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Log $product)
    {
        $product->delete();
   
        return $this->sendResponse([], 'Product deleted successfully.');
    }


}