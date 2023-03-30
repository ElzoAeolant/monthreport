@extends('layouts.app', ['activePage' => 'location', 'activeButton' => 'EnergyManagement', 'title' => 'Smart Reports', 'navName' => 'Locations'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card data-tables">

                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Locations') }}</h3>
                                <p class="text-sm mb-0">
                                    {{ __('Management') }}
                                </p>
                            </div>
                            @if (auth()->user()->can('create', App\Location::class))
                            <div class="col-4 text-right">
                                <a href="{{ route('location.create') }}" class="btn btn-sm btn-primary">{{ __('Add Location') }}</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        @include('alerts.success')
                        @include('alerts.errors')
                    </div>

                    <div class="table-responsive py-4" id="categories-table">
                        <table id="datatables" class="table table-striped table-bordered table-hover" cellspacing="0"
                            width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('Tag') }}</th>
                                    <th>{{ __('Cluster') }}</th>
                                    <th>{{ __('Creation data') }}</th>
                                    @can('manage-items', App\Models\User::class)
                                    <th class="disabled-sorting text-right">{{ __('Actions') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>{{ __('Tag') }}</th>
                                    <th>{{ __('Cluster') }}</th>
                                    <th>{{ __('Creation data') }}</th>
                                    @can('manage-items', App\Models\User::class)
                                    <th class="text-right">{{ __('Actions') }}</th>
                                    @endcan
                                </tr>
                            </tfoot>
                            <tbody>

                                @foreach ($locations as $location)
                                <tr>
                                    <td>{{ $location->tag }}</td>
                                    <td><span class="badge badge-default" style="background-color:{{ $location->cluster_color }}">{{ $location->cluster_name }}</span></td>
                                    <td>{{ $location->created_at }}</td>
                                    @can('manage-items', App\Models\User::class)
                                    <td class="text-right">
                                        @if (auth()->user()->can('update', $location) || auth()->user()->can('delete',
                                        $location))
                                        @can('update', $location)
                                        <a href="{{ route('location.edit', $location->id) }}"
                                            class="btn btn-link btn-warning edit d-inline-block"><i
                                                class="fa fa-edit"></i></a>
                                        @endcan
                                        <!--$location->items->isEmpty() && -->
                                        @if (auth()->user()->can('delete', $location))
                                        <form class="d-inline-block"
                                            action="{{ route('location.destroy', $location->id) }}" method="POST">
                                            @method('delete')
                                            @csrf
                                            <a class="btn btn-link btn-danger "
                                                onclick="confirm('{{ __('¿Está seguro?') }}') ? this.parentElement.submit() : ''"><i
                                                    class="fa fa-times"></i></a>
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
    $(document).ready(function () {
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
        table.on('click', '.remove', function (e) {
            $tr = $(this).closest('tr');
            table.row($tr).remove().draw();
            e.preventDefault();
        });

        //Like record
        table.on('click', '.like', function () {
            alert('You clicked on Like button');
        });
    });
</script>
@endpush