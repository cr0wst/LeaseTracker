@extends('layouts.default')
@section('pageName', 'Add a Vehicle')
@section('content')
    <h1>Add a Vehicle</h1>
    <div class="row">
        <ul class="list-group">
            {{-- Should setup a template for this. --}}
            <li class="list-group-item">
                <h2>Vehicle Details</h2>

                @include('includes/vehicleform')
                {!! Form::close() !!}
            </li>
        </ul>
    </div>
@endsection
