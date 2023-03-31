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
                                    <h3 class="mb-0">{{ __('Tags') }}</h3>
                                    <p class="text-sm mb-0">
                                        {{ __('Management') }}
                                    </p>
                                </div>
                                @can('create', App\Tag::class)
                                    <div class="col-4 text-right">
                                        <a href="{{ route('hotel.report') }}" class="btn btn-sm btn-primary">{{ __('Create Report') }}</a>
                                    </div>
                                @endcan
                            </div>
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