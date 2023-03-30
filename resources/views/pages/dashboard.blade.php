@extends('layouts/app', ['activePage' => 'dashboard', 'activeButton' => 'AEOLANTMANAGMENT', 'title' => 'AEOLANT : Plataforma de generación de reportes', 'navName' => 'Dashboard'])

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-sm-12">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-chart text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Size on DB') }}</p>
                                    <h4 class="card-title">{{ $sizeof }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-calendar-o"></i> {{ __('Since') }} {{$update_sizeof }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-sm-12">
                <div class="card card-stats">
                    <div class="card-body ">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center icon-warning">
                                    <i class="nc-icon nc-light-3 text-success"></i>
                                </div>
                            </div>
                            <div class="col-7">
                                <div class="numbers">
                                    <p class="card-category">{{ __('Records') }}</p>
                                    <h4 class="card-title">{{ $records }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ">
                        <hr>
                        <div class="stats">
                            <i class="fa fa-refresh"></i> {{ $update_records }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        const ctx = document.getElementById('chartEnergy');

        new Chart(ctx, {
            type: 'bar',
            data:{
                labels: [{!!$labels!!}],
                datasets: [
                    @foreach ($th as $key => $value) 
                        {
                            label: "{!!$value["label"]!!}",
                            data: [{!!implode(', ', $value["data"])!!}],
                            borderColor: '{!!$value["borderColor"]!!}',
                            backgroundColor: '{!!$value["backgroundColor"]!!}',
                        },
                    @endforeach 
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });
        const ctx2 = document.getElementById('myChart2');

        new Chart(ctx2, {
            type: 'bar',
            data:{
                labels: [{!!$labels!!}],
                datasets: [
                    @foreach ($logs as $key => $value) 
                        {
                            label: "{!!$value["label"]!!}",
                            data: [{!!implode(', ', $value["data"])!!}],
                            borderColor: '{!!$value["borderColor"]!!}',
                            backgroundColor: '{!!$value["backgroundColor"]!!}',
                        },
                    @endforeach 
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            }
        });

        //setTimeout("location.reload(true);", 5000);
    });
</script>
@endpush