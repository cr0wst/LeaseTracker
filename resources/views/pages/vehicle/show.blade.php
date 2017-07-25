@extends('layouts.default')
@section('pageName', 'Vehicles')

@section('content')
    <div class="row">
        <div class="col-sm-1">
            <img src="{{ $vehicle->image_url }}" class="vehicle-image img-circle">
        </div>
        <div class="col-sm-11">
            <h1>{{ $vehicle->make_model }} <small>Owner: {{ $vehicle->name }}</small></h1>
        </div>
    </div>
    <br>
    @if (Session::get('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif
    <div class="row">
        <ul class="list-group">
            {{-- Should setup a template for this. --}}
            <li class="list-group-item">
                <h2>Vehicle Details</h2>
                <table class="table table-bordered">
                    {{-- Should def setup a template for this as it's used twice now --}}
                    <tbody>
                    <tr>
                        <th>Cost Per Mile</th>
                        <td>{{ $vehicle->cost_per_mile }}</td>
                    </tr>
                    <tr>
                        <th>Total Months</th>
                        <td>{{ $vehicle->months }}</td>
                    </tr>
                    <tr>
                        <th>Starting Miles</th>
                        <td>{{ $vehicle->starting_mileage }}</td>
                    </tr>
                    <tr>
                        <th>Total Allowable Miles</th>
                        <td>{{ $vehicle->total_allowable_mileage }}</td>
                    </tr>
                    </tbody>
                </table>
                <div class="text-right">
                    <a href="{{ url('vehicle') }}/{{$vehicle->id }}/edit">Edit Vehicle</a>
                </div>
                <h2>Cost Summary</h2>
                <table class="table table-bordered">
                    {{-- Should def setup a template for this as it's used twice now --}}
                    <tbody>
                    <tr>
                        <th>Miles Remaining</th>
                        <td>{{ $vehicle->costData->milesRemaining }}</td>
                    </tr>
                    <tr>
                        <th>Miles Per Month</th>
                        <td>{{ $vehicle->costData->milesPerMonth }}</td>
                    </tr>
                    <tr>
                        <th>Predicted Overage</th>
                        <td>{{ $vehicle->costData->predictedOverage }}</td>
                    </tr>
                    <tr>
                        <th>Predicted Cost</th>
                        <td>{{ $vehicle->costData->getFormattedCost() }}</td>
                    </tr>
                    <tr>
                        <th>Months Remaining</th>
                        <td>{{ $vehicle->costData->monthsRemaining }}</td>
                    </tr>
                    </tbody>
                </table>

                <h2>Mileage Entries</h2>
                <div class="text-right">
                    <a href="{{ url('mileage') }}/{{$vehicle->id }}/create">Add Mileage</a>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Current Mileage</th>
                            <th>Diff. to Previous</th>
                            <th>Date of Entry</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($mileEntries as $mileage)
                        <tr>
                            <td>{{ $mileage->date }}</td>
                            <td>{{ $mileage->currentMileage }}</td>
                            <td>{{ $mileage->differenceToPrevious }}</td>
                            <td>{{ $mileage->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <a href="{{ url('mileage') }}/{{$vehicle->id }}/create">Add Mileage</a>
                </div>
            </li>
        </ul>
    </div>
@endsection
