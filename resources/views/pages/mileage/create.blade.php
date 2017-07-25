@extends('layouts.default')
@section('pageName', 'Add a Vehicle')
@section('content')
    <h1>Add a Mileage Reading</h1>
    <div class="row">
        <ul class="list-group">
            {{-- Should setup a template for this. --}}
            <li class="list-group-item">
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

                    <h2>Mileage Information</h2>
                    {!! Form::open(['route' => ['mileage.store', $vehicle->id], 'class' => 'form form-inline']) !!}
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            Please fix the following: <br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="form-group">
                        {!! Form::label('Date') !!}
                        {!! Form::text('date', null,
                            array(
                                'id' => 'datepicker',
                                'class' => 'form-control',
                                'placeholder' => '2017-01-01'
                            )) !!}

                        {!! Form::label('Current Mileage') !!}
                        {!! Form::text('currentMileage', null,
                            array(
                                'class' => 'form-control',
                                'placeholder' => '12345'
                            )) !!}
                    </div>
                            {!! Form::submit('Add Mileage Entry',
                                array('class' => 'btn btn-primary'
                                )) !!}
                {!! Form::close() !!}

                <h2>Mileage Entries</h2>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Current Mileage</th>
                        <th>Date of Entry</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mileEntries as $mileage)
                        <tr>
                            <td>{{ $mileage->date }}</td>
                            <td>{{ $mileage->currentMileage }}</td>
                            <td>{{ $mileage->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </li>
        </ul>
    </div>
@endsection
