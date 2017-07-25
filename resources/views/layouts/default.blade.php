<html>
    <head>
        <title>Lease Tracker - @yield('pageName')</title>
        <meta charset="utf-8">
        <meta name="description" content="">

        <link rel="stylesheet" href="/css/app.css">
        {{-- Should probably include this using webpack --}}
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
    </head>
    <body>
        <nav class="navbar navbar-inverse" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Lease Tracker</a>
                </div>

                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        {{-- There has to be a better way to do this?  I can pull the URL using helper methods,
                        surely I can get the active controller too.

                        I was using Route::is, but I noticed that it wasn't working for paths like /vehicle/1

                        I opted to use a ViewComposer along with Route::group.
                        --}}
                        <li class="{{ $_active_menu === 'vehicle' ? 'active' : '' }}">
                            <a href="{{ url('/') }}">Manage Vehicle(s)</a>
                        </li>
                        <li>
                            <a href="{{ url('/vehicle') }}/create">Create Vehicle</a>
                        </li>
                        @if ($_active_menu === 'mileage')
                            <li class="{{ $_active_menu === 'mileage' ? 'active' : '' }}">
                                <a href="/mileage/{{$vehicle->id}}/create">Mileage</a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                @yield('content')
                </div>
            </div>
            <div class="row">
                <hr>
                <div class="col-sm-12 text-center">
                    Lease Tracker : Steve Crow : steve@smcrow.net : This is a work in progress and was used to learn and demonstrate Laravel.
                </div>
            </div>
        </div>

        <script type="application/javascript" src="/js/app.js"></script>
        <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
        <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
        <script>
            $(function() {
                $( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
            });
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>
    </body>
</html>