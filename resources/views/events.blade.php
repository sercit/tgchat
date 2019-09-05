@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Мои записи</div>
            <div class="panel-body">
                {!! Form::open(array('route' => 'events.add', 'method'=>'POST', 'files'=>'true')) !!}
                <div class="row">
                    <div class="col-xs-12">
                        @if (Session::has('success'))
                            <div class="alert alert-success">
                                {{ Session::get('success') }}
                            </div>
                        @elseif (Session::has('warning'))
                            <div class="alert alert-danger">
                                {{ Session::get('warning') }}
                            </div>
                        @endif

                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('service_id', 'Название услуги') !!}
                            <div class="">
                                {!! Form::select('service_id', $services,null, ['class'=>'form-control']) !!}
                                {!! $errors->first('service_id','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('client_id', 'Клиент') !!}
                            <div class="">
                                {!! Form::select('client_id', $clients,null, ['class'=>'form-control']) !!}
                                {!! $errors->first('client_id','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('start_date', 'Начало') !!}
                            <div class="">
                                {!! Form::text('start_date', null, ['class'=>'form-control','autocomplete'=>'off']) !!}
                                {!! $errors->first('start_date','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-1 text-center">&nbsp;<br/>
                        {!! Form::submit('Добавить запись', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">MY Event Details</div>
            <div class="panel-body">
                {!! $calendar_details->calendar() !!}
            </div>
        </div>
    </div>
@endsection

@section('pageScript')
    {!! $calendar_details->script() !!}
    <link rel="stylesheet" type="text/css" href="js/jquery.datetimepicker.css"/>
    <script src="js/build/jquery.datetimepicker.full.js"></script>
    <script>
            $('#start_date').datetimepicker({
                format:'Y-m-d H:i:00',
                lang:'ru',
                step:15,
                minDate:0,
            });
    </script>
@endsection
