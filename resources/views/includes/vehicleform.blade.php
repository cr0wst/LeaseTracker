@if( isset($vehicle) )
    {!! Form::model($vehicle, ['route' => ['vehicle.update', $vehicle->id], 'class' => 'form', 'method' => 'PUT']) !!}
@else
    {!! Form::open(['route' => 'vehicle.store', 'class' => 'form']) !!}
@endif
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
    {!! Form::label('Vehicle Make/Model') !!}
    {!! Form::text('make_model', null,
        array(
            'class' => 'form-control',
            'placeholder' => 'Chevy Impala'
        )) !!}

    {!! Form::label('Owner Name') !!}
    {!! Form::text('name', null,
        array(
            'class' => 'form-control',
            'placeholder' => 'John Doe'
        )) !!}
</div>
<h2>Lease Details</h2>
<div class="form-group">
    {!! Form::label('start_date', 'Start Date', array('class' => 'control-label')) !!}
    {!! Form::text('start_date', null,
        array(
            'id' => 'datepicker',
            'class' => 'form-control',
            'placeholder' => '2017-01-01'
        )) !!}

    {!! Form::label('cost_per_mile', 'Cost Per Mile', array('class' => 'control-label')) !!}

    {!! Form::text('cost_per_mile', null,
        array(
            'class' => 'form-control',
            'placeholder' => '0.25'
        )) !!}

    {!! Form::label('months', 'Months', array('class' => 'control-label')) !!}
    {!! Form::text('months', null,
        array(
            'class' => 'form-control',
            'placeholder' => '24'
        )) !!}



    {!! Form::label('starting_mileage', 'Starting Miles', array('class' => 'control-label')) !!}
    {!! Form::text('starting_mileage', null,
        array(
            'class' => 'form-control',
            'placeholder' => '35'
        )) !!}

    {!! Form::label('total_allowable_mileage', 'Total Allowable Miles', array('class' => 'control-label')) !!}
    {!! Form::text('total_allowable_mileage', null,
        array(
            'class' => 'form-control',
            'placeholder' => '24000'
        )) !!}
</div>

<div class="form-group">
    @if( isset($vehicle) )
        {!! Form::submit('Update Vehicle',
            array('class' => 'btn btn-primary'
            )) !!}

        {!! Form::button('Delete Vehicle',
            array(
                'class' => 'btn btn-danger',
                'onClick' => 'document.forms.delete.submit()'
            )) !!}
        {!! Form::close() !!}

        {{-- The delete form which will get triggered via javascript --}}
        {!! Form::open(['route' => ['vehicle.destroy', $vehicle->id], 'class' => 'form', 'name' => 'delete', 'method' => 'DELETE']) !!}
        {!! Form::close() !!}
    @else
        {!! Form::submit('Add a Vehicle',
            array('class' => 'btn btn-primary'
            )) !!}
        {!! Form::close() !!}
    @endif
</div>
