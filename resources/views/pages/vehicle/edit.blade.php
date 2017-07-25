@extends('layouts.default')
@section('pageName', 'Edit a Vehicle')
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

                @include('includes/vehicleform')
            </li>
        </ul>
    </div>
@endsection
