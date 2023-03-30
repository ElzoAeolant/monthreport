@extends('layouts.app', ['activePage' => 'location', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Locations'])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header ">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">{{ __('Edit Location') }}</h3>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('location.index') }}" class="btn btn-sm btn-warning">{{ __('Back to list') }}</a>
                    </div>
                </div>
            </div>
            <div class="card-body ">
                <form method="post" action="{{ route('location.update', $location->id) }}">
                    @csrf
                    @method('patch')
                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('tag') ? ' has-danger' : '' }}">
                                {{ __('Name') }}</label>
                            <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="tag" maxlength="30" value="{{ old('tag', $location->tag) }}" placeholder="{{ __('Etiqueta') }}" autofocus>
                            @include('alerts.feedback', ['field' => 'tag'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label for="country"></label>
                            <label class="form-control-label{{ $errors->has('cluster_id') ? ' has-danger' : '' }}">{{
                                __('Cluster') }}</label>
                            <select class="form-control" name="cluster_id">
                                <option value="">Selecciona un cluster</option>
                                @foreach ($clusters as $cluster)
                                <option value="{{$cluster->id}}" {{ old('cluster_id' , $location->cluster_id)==$cluster->id ? "selected":""}}>{{$cluster->name}}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'cluster_id'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label for="country"></label>
                            <label class="form-control-label{{ $errors->has('state_id') ? ' has-danger' : '' }}">{{
                                __('Estado') }}</label>
                            <select class="form-control" name="state_id" id="country-dropdown">
                                <option value="">Selecciona un estado</option>
                                @foreach ($countries as $country)
                                <option value="{{$country->id}}" {{ old('state_id', $location->state_id)==$country->id ? "selected":""}}>{{$country->nombre}}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'state_id'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('municipality_id') ? ' has-danger' : '' }}">{{
                                __('Municipio') }}</label>
                            <select class="form-control" name="municipality_id" id="state-dropdown">
                            </select>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'municipality_id'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('town_id') ? ' has-danger' : '' }}">{{
                                __('Localidad') }}</label>
                            <select class="form-control" name="town_id" id="city-dropdown">
                            </select>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'town_id'])
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('latitude') ? ' has-danger' : '' }}">
                                {{ __('Latitude') }}</label>
                            <input type="text" class="form-control{{ $errors->has('latitude') ? ' is-invalid' : '' }}" name="latitude" maxlength="30" value="{{ old('latitude', $location->latitude) }}" placeholder="{{ __('latitude') }}" autofocus>
                            @include('alerts.feedback', ['field' => 'latitude'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('longitude') ? ' has-danger' : '' }}">
                                {{ __('Longitude') }}</label>
                            <input type="text" class="form-control{{ $errors->has('longitude') ? ' is-invalid' : '' }}" name="longitude" maxlength="30" value="{{ old('longitude', $location->longitude) }}" placeholder="{{ __('longitude') }}" autofocus>
                            @include('alerts.feedback', ['field' => 'longitude'])
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('ssid') ? ' has-danger' : '' }}">
                                {{ __('WIFI SSID') }}</label>
                            <input type="text" class="form-control{{ $errors->has('ssid') ? ' is-invalid' : '' }}" name="ssid" maxlength="30" value="{{ old('ssid', $location->ssid) }}" placeholder="{{ __('ssid') }}" autofocus>
                            @include('alerts.feedback', ['field' => 'ssid'])
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('ssid_password') ? ' has-danger' : '' }}">
                                {{ __('SSID Password') }}</label>
                            <input type="text" class="form-control{{ $errors->has('ssid_password') ? ' is-invalid' : '' }}" name="ssid_password" maxlength="30" value="{{ old('ssid_password', $location->ssid_password) }}" placeholder="{{ __('ssid_password') }}" autofocus>
                            @include('alerts.feedback', ['field' => 'ssid_password'])
                        </div>
                    </fieldset>

                    <fieldset>
                        <div class="form-group">
                            <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">{{
                                __('Description') }}</label>
                            <textarea name="description" class="form-control{{ $errors->has('description') ? ' has-error is-invalid' : '' }}" rows="3" maxlength="300" placeholder="{{ __('Description') }}" autofocus> {{ old('description', $location->description) }}</textarea>
                            <small class="form-text text-muted"></small>
                            @include('alerts.feedback', ['field' => 'description'])
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-warning">{{ __('Save changes') }}</button>
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
<script>
    $(document).ready(function() {
        let oldCountry = '{{ old('state_id', $location->state_id) }}';
        if (oldCountry != '') {
            let country_id = oldCountry;
            $("#state-dropdown").html('');
            $.ajax({
                url: "{{url('get-municipality-by-state')}}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#state-dropdown').html('<option value="">Selecciona un municipio</option>');
                    $.each(result.states, function(key, value) {
                        $("#state-dropdown").append('<option value="' + value.id + '"' + (value.id == '{{ old('municipality_id', $location->municipality_id) }}' ? 'selected' : '') + '>' + value.nombre + '</option>');
                    });
                    $('#city-dropdown').html('<option value="">Selecciona primero el municipio</option>');
                }
            });
        }

        let oldState = '{{ old('municipality_id', $location->municipality_id) }}';
        if (oldState != '') {
            let state_id = oldState;
            $("#city-dropdown").html('');
            $.ajax({
                url: "{{url('get-town-by-municipality')}}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#city-dropdown').html('<option value="">Selecciona una localidad</option>');
                    $.each(result.cities, function(key, value) {
                        $("#city-dropdown").append('<option value="' + value.id + '"' + (value.id == '{{ old('town_id', $location->town_id) }}' ? 'selected' : '') + '>' + value.nombre + '</option>');
                    });
                }
            });
        }

        $('#country-dropdown').on('change', function() {
            let country_id = this.value;
            $("#state-dropdown").html('');
            $.ajax({
                url: "{{url('get-municipality-by-state')}}",
                type: "POST",
                data: {
                    country_id: country_id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#state-dropdown').html('<option value="">Selecciona un municipio</option>');
                    $.each(result.states, function(key, value) {
                        $("#state-dropdown").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    });
                    $('#city-dropdown').html('<option value="">Selecciona primero el municipio</option>');
                }
            });
        });
        $('#state-dropdown').on('change', function() {
            let state_id = this.value;
            $("#city-dropdown").html('');
            $.ajax({
                url: "{{url('get-town-by-municipality')}}",
                type: "POST",
                data: {
                    state_id: state_id,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function(result) {
                    $('#city-dropdown').html('<option value="">Selecciona una localidad</option>');
                    $.each(result.cities, function(key, value) {
                        $("#city-dropdown").append('<option value="' + value.id + '">' + value.nombre + '</option>');
                    });
                }
            });
        });
    });
</script>
@endpush