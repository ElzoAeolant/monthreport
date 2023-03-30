@extends('layouts.app', ['activePage' => 'tracking', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Device tracking' ])

@section('content')
<div class="content">
    <div class="col-md-12">
        <div class="card ">
            <div class="card-header ">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h3 class="mb-0">{{ __('Create Device Tracking') }}</h3>
                    </div>
                    <div class="col-4 text-right">
                        <a href="{{ route('tracking.index') }}" class="btn btn-sm btn-warning">{{ __('Back to list') }}</a>
                    </div>
                </div>
            </div>
            <div class="card-body ">
                    <h6 class="heading-small text-muted mb-4">{{ __('Device Code For Arduino') }}</h6>
                    <fieldset>
                        <div class="w-100 p-3" class="form-group">
                            <label class="form-control-label{{ $errors->has('description') ? ' has-danger' : '' }}">{{
                                __('Description') }}</label>
                            <textarea disabled id="myInput" style="width: 100%; height: 400px; background-color:#CDFCFA ;" class="form-control" rows="8" autofocus>{{ $code }}</textarea>
                        </div>
                    </fieldset>
            </div>
            <div class="card-footer">
                <div class="col-4 text-right">
                    <button class="btn btn-primary" id="btnCopy"><i class="fa fa-copy"></i> COPIAR</button>
                </div>
            </div>
        </div>
    </div>
    <div class="form-outline">
</div>
</div>
@endsection
@push('js')
<script src="https://cdn.ckeditor.com/ckeditor5/19.1.1/classic/ckeditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

<script type="text/javascript">
    var idCapture = "";
    $("#btnCopy").click(function () {
        // Get the text field
        var copyText = document.getElementById("myInput");

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        demo.showNotification('top','right','Texto copiado!')
    });

    $(".opencamera").click(function() {
        Webcam.set({
            width: 320,
            height: 240,
            dest_width: 640,
            dest_height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90
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