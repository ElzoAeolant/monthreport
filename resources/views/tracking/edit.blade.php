@extends('layouts.app', ['activePage' => 'tracking', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Device tracking' ])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header ">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">{{ __('Edit Device Tracking') }}</h3>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('tracking.index') }}" class="btn btn-sm btn-warning">{{ __('Back to list') }}</a>
                    </div>
                </div>
            </div>

            <div class="card-body ">
                <form method="post" action="{{ route('tracking.update', $tracking->id) }}">
                    @csrf
                    @method('patch')
                    <h6 class="heading-small text-muted mb-4">{{ __('Device informations') }}</h6>
                    <fieldset>
                        <div class="form-group">
                            <label for="country"></label>
                            <label class="form-control-label{{ $errors->has('device_id') ? ' has-danger' : '' }}">{{ __('Device') }}</label>
                            <select class="form-control" name="device_id">
                                <option value="">Selecciona un dispositivo</option>
                                @foreach ($devices as $device)
                                <option value="{{$device->id}}" {{ old('device_id',$tracking->device_id)==$device->id ? "selected":""}}>{{$device->label}}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'device_id'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label for="country"></label>
                            <label class="form-control-label{{ $errors->has('location_id') ? ' has-danger' : '' }}">{{ __('Location') }}</label>
                            <select class="form-control" name="location_id">
                                <option value="">Selecciona un dispositivo</option>
                                @foreach ($locations as $location)
                                <option value="{{$location->id}}" {{ old('location_id',$tracking->location_id)==$location->id ? "selected":""}}>{{$location->tag}}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'location_id'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group {{ $errors->has('status') ? ' has-danger' : '' }}">
                            <label class="form-control-label">{{ __('Status') }}</label>
                            <div class="form-check form-check-radio">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="status" id="Mantenimiento" value="Mantenimiento" {{ old('status',$tracking->status) == 'Mantenimiento' ? ' checked' : '' }}>
                                    <span class="form-check-sign"></span>
                                    <label class="badge badge-default" style="background-color:{{ $colors['Mantenimiento'] }}">{{ __('-') }}</label> {{ __('Mantenimiento') }}
                                </label>
                            </div>
                            <div class="form-check form-check-radio">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="status" id="Activo" value="Activo" {{ old('status',$tracking->status) == 'Activo' ? ' checked' : '' }}>
                                    <span class="form-check-sign"></span>
                                    <label class="badge badge-default" style="background-color:{{ $colors['Activo'] }}">{{ __('-') }}</label> {{ __('Activo') }}
                                </label>
                            </div>
                            <div class="form-check form-check-radio">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="status" id="Activo (SL)" value="Activo (SL)" {{ old('status',$tracking->status) == 'Activo (SL)' ? ' checked' : '' }}>
                                    <span class="form-check-sign"></span>
                                    <label class="badge badge-default" style="background-color:{{ $colors['Activo (SL)'] }}">{{ __('-') }}</label> {{ __('Activo (SL)') }}
                                </label>
                            </div>
                            <div class="form-check form-check-radio">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="status" id="Inactivo" value="Inactivo" {{ old('status',$tracking->status) == 'Inactivo' ? ' checked' : '' }}>
                                    <span class="form-check-sign"></span>
                                    <label class="badge badge-default" style="background-color:{{ $colors['Inactivo'] }}">{{ __('-') }}</label> {{ __('Inactivo') }}
                                </label>
                            </div>
                            @include('alerts.feedback', ['field' => 'status'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <label>Select Video Source (Camera)</label>

                                    <select id="video-source"></select>
                                    <div class="modal-body">
                                        <div class="col-md-12 mx-auto" id="my_camera"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="col-md-12 mx-auto btn btn-primary" onClick="take_snapshot()">Capturar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>{{ __('Evidencia') }}</label>
                            <div class="row">
                                <div class="col-sm-10">
                                    <button type="button" class="btn btn-primary opencamera" data-id="image_device" data-toggle="modal" data-target="#exampleModalCenter"> {{ __('Device photo') }} </button>
                                    <div id="capture_image_device"></div>
                                    <input type="hidden" name="image_device" value='{{ old('image_device',$tracking->image_outdoor) }}' id="image_device">
                                    @include('alerts.feedback', ['field' => 'image_device'])
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10">
                                    <button type="button" class="btn btn-primary opencamera" data-id="image_indoor" data-toggle="modal" data-target="#exampleModalCenter"> {{ __('Indoor photo') }} </button>
                                    <div id="capture_image_indoor"></div>
                                    <input type="hidden" name="image_indoor" value='{{ old('image_indoor',$tracking->image_outdoor) }}' id="image_indoor">
                                    @include('alerts.feedback', ['field' => 'image_indoor'])
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10">
                                    <button type="button" class="btn btn-primary opencamera" data-id="image_outdoor" data-toggle="modal" data-target="#exampleModalCenter"> {{ __('Outdoor photo') }} </button>
                                    <div id="capture_image_outdoor"></div>
                                    <input type="hidden" name="image_outdoor" value='{{ old('image_outdoor',$tracking->image_outdoor) }}' id="image_outdoor">
                                    @include('alerts.feedback', ['field' => 'image_outdoor'])
                                </div>
                            </div>
                        </div>
                        <!--  -->
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-warning">{{ __('Update device') }}</button>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

<script type="text/javascript">

    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
        console.log("enumerateDevices is not supported.");
    } else{
        navigator.mediaDevices.enumerateDevices().then((devices) => {
            let videoSourcesSelect = document.getElementById("video-source");

                // Iterate over all the list of devices (InputDeviceInfo and MediaDeviceInfo)
                for (let i = devices.length-1; i >= 0; i--) {
                    let option = new Option();
                    option.value = devices[i].deviceId;

                    // According to the type of media device
                    switch(devices[i].kind){
                        // Append device to list of Cameras
                        case "videoinput":
                            option.text = devices[i].label || `Camera ${videoSourcesSelect.length + 1}`;
                            videoSourcesSelect.appendChild(option);
                            break;
                    }
                }
            }).catch(function (e) {
                console.log(e.name + ": " + e.message);
            });
    }

    $("#video-source").change(function() {
        Webcam.set({
            width: 320,
            height: 240,
            dest_width: 640,
            dest_height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
				width: 320, // { exact: 320 },
				height: 240, // { exact: 180 },
				facingMode: 'user',
				deviceId: {exact: $(this).val() },
				frameRate: 30
			}
        });
        Webcam.attach('#my_camera');
    });


    var idCapture = "";
    let oldData = {{ (old('image_device',$tracking->image_device)!="" || old('image_indoor',$tracking->image_indoor)!="" || old('image_outdoor',$tracking->image_outdoor)!="")? 'true' : 'false'}} ;
    if(oldData){
        let imageDevice = {{ (old('image_device',$tracking->image_device)!= "")?'true':'false'}};
        if (imageDevice){
            document.getElementById('capture_image_device').innerHTML = '<img width="300" height="240" src="{{ old('image_device',$tracking->image_device) }}"/>';
        }
        let imageIndoor = {{ (old('image_indoor',$tracking->image_indoor)!= "")?'true':'false'}};
        if (imageDevice){
            document.getElementById('capture_image_indoor').innerHTML = '<img width="300" height="240" src="{{ old('image_indoor',$tracking->image_indoor) }}"/>';
        }
        let imageOutdoor = {{ (old('image_outdoor',$tracking->image_outdoor)!= "")?'true':'false'}};
        if (imageDevice){
            document.getElementById('capture_image_outdoor').innerHTML = '<img width="300" height="240" src="{{ old('image_outdoor',$tracking->image_outdoor) }}"/>';
        }
        
    };
    

    $(".opencamera").click(function() {
        Webcam.set({
            width: 320,
            height: 240,
            dest_width: 640,
            dest_height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            constraints: {
				width: 320, // { exact: 320 },
				height: 240, // { exact: 180 },
				facingMode: 'user',
				deviceId: {exact: $('#video-source').val() },
				frameRate: 30
			}
        });
        Webcam.attach('#my_camera');
        idCapture = $(this).attr('data-id');
    });

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            $("#" + idCapture).val(data_uri);
            $('#exampleModalCenter').modal('hide');
            Webcam.reset();
            document.getElementById('capture_' + idCapture).innerHTML = '<img width="300" height="240" src="' + data_uri + '"/>';
        });
    }
</script>
@endpush