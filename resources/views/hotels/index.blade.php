@extends('layouts.app', ['activePage' => 'tag', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Tags' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Reportes') }}</h3>
                                    <p class="text-sm mb-0">
                                        {{ __('Management') }}
                                    </p>
                                </div>
                                @can('create', App\Tag::class)
                                    
                                @endcan
                            </div>
                        </div>

                        <div class="card-body ">
                            <form method="post" action="{{ route('hotel.report') }}" >
                                @csrf
                                <h6 class="heading-small text-muted mb-4">{{ __('Generar reporte') }}</h6> 
                                <fieldset>
                                <fieldset>
                                <fieldset>
                                    <div class="col-12 d-flex justify-content-center">
                                        <div style="margin: 0 10px;">
                                            <label id= "checkIn" for="checkIn">Fecha 1:</label>
                                            <br>
                                            <input type="date" name="checkIn">
                                        </div>
                                        <div style="margin: 0 10px;">
                                            <label for="checkOut">Fecha 2:</label>
                                            <br>
                                            <input type="date" id="checkOut" name="checkOut">
                                        </div>
                                    </div>
                                </fieldset>
                                <br>
                                <fieldset class="text-center">
                                    <div class="form-group">
                                        <label for="hotel" class="font-weight-bold">Hotel:</label>
                                        <select name="hotel" id="hotel">
                                            <option value="Jade">Jade</option>
                                            <option value="Ophelia">Ophelia</option>
                                            
                                        </select>
                                    </div>
                                </fieldset>

                                <fieldset>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <button href="{{ route('hotel.report') }}" class="btn btn-warning">{{ __('Create Report') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>
                        <p>{!!$data!!}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatables').DataTable({
                "pagingType": "full_numbers",
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.12.1/i18n/es-MX.json"
                }

            });
        
            var table = $('#datatables').DataTable();

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });

            //Like record
            table.on('click', '.like', function() {
                alert('You clicked on Like button');
            });
        });
    </script>
@endpush