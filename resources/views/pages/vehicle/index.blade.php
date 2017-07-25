@extends('layouts.default')
@section('pageName', 'Vehicles')

@section('content')
    <h1>Registered Vehicles</h1>
    @if (Session::get('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif
        <ul class="list-group">
        @if (count($vehicles) > 0)
            @foreach ($vehicles as $vehicle)
            {{-- Should setup a template for this. --}}
            <li class="list-group-item">
                <div class="row">
                <div class="col-sm-1">
                    <img src="{{ $vehicle->image_url }}" class="vehicle-image img-circle">
                </div>
                <div class="col-sm-5">
                    <h2>{{ $vehicle->make_model }} <small>Owner: {{ $vehicle->name }} <hr> Term: {{ $vehicle->start_date }} - {{ $vehicle->costData->endDate }}</small></h2>
                </div>
                    <div class="col-sm-6">
                        <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Miles Remaining</th>
                            <th>Months Remaining</th>
                            <th>Predicted Cost</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $vehicle->costData->milesRemaining }}</td>
                            <td>{{ $vehicle->costData->monthsRemaining }}</td>
                            <td>{{ $vehicle->costData->getFormattedCost() }}</td>
                        </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="row">
                    <div class="text-left col-sm-6">
                        <a href="{{ url('vehicle') }}/{{ $vehicle->id }}">Show Details</a>
                    </div>
                    <div class="text-right col-sm-6">
                        <a href="{{ url('vehicle') }}/{{$vehicle->id }}/edit">Edit Vehicle</a> |
                        <a href="{{ url('mileage') }}/{{$vehicle->id }}/create">Add Mileage Reading</a>
                    </div>
                </div>

            </li>
        @endforeach
        @else
            <li class="list-group-item">
                <div class="row">
                    <div class="col-lg-3"><img src="sad.png"></div>
                    <div class="col-lg-9 no-vehicles">You don't have any vehicles registered.  Don't you want to add a vehicle?</div>
                </div>
            </li>
        @endif
        </ul>
    <a href="{{ url('vehicle') }}/create">Add a Vehicle</a>
@endsection