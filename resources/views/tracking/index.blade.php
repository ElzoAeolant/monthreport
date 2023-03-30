@extends('layouts.app', ['activePage' => 'tracking', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Device tracking' ])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card data-tables">

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Device Tracking') }}</h3>
                                    <p class="text-sm mb-0">
                                        {{ __('Management') }}
                                    </p>
                                </div>
                                @can('create', App\Tag::class)
                                    <div class="col-4 text-right">
                                        <a href="{{ route('tracking.create') }}" class="btn btn-sm btn-primary">{{ __('Add device') }}</a>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            @include('alerts.success')
                            @include('alerts.errors')
                        </div>

                        <div class="table-responsive py-4" id="devices-table">
                            <table id="datatables" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="width:100% ">
                                <thead>
                                    <tr>
                                        <th>{{ __('Device') }}</th>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Last Date Record') }}</th>
                                        <th>{{ __('Online') }}</th>
                                        @can('manage-items', App\Models\User::class)
                                            <th class="disabled-sorting text-right">{{ __('Actions') }}</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>{{ __('Device') }}</th>
                                        <th>{{ __('Location') }}</th> 
                                        <th>{{ __('Last Date Record') }}</th>
                                        <th>{{ __('Online') }}</th>
                                        @can('manage-items', App\Models\User::class)
                                            <th class="text-right">{{ __('Actions') }}</th>
                                        @endcan
                                    </tr>
                                </tfoot>
                                <tbody>

                                    @foreach ($tracking as $dt)
                                        <tr>
                                            <td><span class="badge badge-default" style="background-color:{{ $dt->location->cluster->color }}">{{ $dt->device->label }}</span></td>
                                            <td>{{ $dt->location->tag }}</td>
                                            <td>{{ $dt->dateLastRecord }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-{{$dt->connection}}">o</button>
                                                {!!$dt->lastRecord!!}
                                            </td>
                                            @can('manage-items', App\Models\User::class)
                                                <td class="text-right">
                                                    @if (auth()->user()->can('update', $dt) || auth()->user()->can('delete', $dt))
                                                        @can('view', $dt)
                                                            <a href="{{ route('tracking.show', $dt->id) }}" class="btn btn-link btn-info edit d-inline-block"><i class="fa fa-eye"></i></a>
                                                        @endcan
                                                        @can('update', $dt)
                                                            <a href="{{ route('tracking.edit', $dt->id) }}" class="btn btn-link btn-warning edit d-inline-block"><i class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @if (auth()->user()->can('delete', $dt))
                                                            <form class="d-inline-block" action="{{ route('tracking.destroy', $dt->id) }}" method="POST">
                                                                @method('delete')
                                                                @csrf
                                                                <a class="btn btn-link btn-danger " onclick="confirm('{{ __('Are you sure you want to delete this device?') }}') ? this.parentElement.submit() : ''"s><i class="fa fa-times"></i></a>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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