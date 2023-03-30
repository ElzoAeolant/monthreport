@extends('layouts.app', ['activePage' => 'device', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Devices' ])

@section('content')
    <div class="content">
        <div class="col-md-12">
            <div class="card ">
                <div class="card-header ">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Edit Device') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('device.index') }}" class="btn btn-sm btn-warning">{{ __('Back to list') }}</a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body ">
                    <form method="post" action="{{ route('device.update', $device->id) }}" >
                        @csrf 
                        @method('patch')
                        <h6 class="heading-small text-muted mb-4">{{ __('Device informations') }}</h6> 
                        <fieldset>
                            <div class="form-group">
                                <label class="form-control-label{{ $errors->has('token') ? ' has-danger' : '' }}">
                                    {{ __('Token') }}</label>
                                <input type="text" class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}" name="token" maxlength="30" value="{{ old('token', $device->token) }}" placeholder="{{ __('Token') }}" autofocus>
                                @include('alerts.feedback', ['field' => 'token'])
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group">
                                <label for="example-color-input" class="form-control-label{{ $errors->has('label') ? ' has-danger' : '' }}">{{__('Label')}}</label>
                                <input class="form-control{{ $errors->has('label') ? ' is-invalid' : '' }}" name="label" maxlength="30" value="{{ old('label', $device->label) }}" placeholder="{{__('Label')}}">
                                @include('alerts.feedback', ['field' => 'label'])
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group {{ $errors->has('status') ? ' has-danger' : '' }}">
                                <label class="form-control-label">{{ __('Status') }}</label>
                                <div class="form-check form-check-radio">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="status" id="StandBy" value="StandBy" {{ old('status', $device->status) == 'StandBy' ? ' checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        <label class="badge badge-default" style="background-color:{{ $colors['StandBy'] }}">{{ __('-') }}</label> {{ __('StandBy') }}
                                    </label>
                                </div>
                                <div class="form-check form-check-radio">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="status" id="Operativo" value="Operativo" {{ old('status', $device->status) == 'Operativo' ? ' checked' : '' }}>
                                        <span class="form-check-sign"></span>
                                        <label class="badge badge-default" style="background-color:{{ $colors['Operativo'] }}">{{ __('-') }}</label> {{ __('Operativo') }}
                                    </label>
                                </div>
                                <div class="form-check form-check-radio">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="status" id="No Operativo" value="No Operativo" {{ old('status', $device->status) == 'No Operativo' ? ' checked' : '' }}>
                                        <span class="form-check-sign" ></span>
                                        <label class="badge badge-default" style="background-color:{{ $colors['No Operativo'] }}">{{ __('-') }}</label> {{ __('No Operativo') }}
                                    </label>
                                </div>
                                @include('alerts.feedback', ['field' => 'status'])
                            </div>
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
    <script>
    $(document).ready(function () {
        ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then( editor => {
            } )
            .catch( error => {
        } );
        
    });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Init Sliders
            $('.datepicker').datetimepicker({
                format: 'DD-MM-YYYY',
                icons: {
                    time: "fa fa-clock-o",
                    date: "fa fa-calendar",
                    up: "fa fa-chevron-up",
                    down: "fa fa-chevron-down",
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-screenshot',
                    clear: 'fa fa-trash',
                    close: 'fa fa-remove'
                }
            });
        });
    </script>
@endpush