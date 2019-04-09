@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Services</div>
            <div class="panel-body">
                {!! Form::open(array('route' => 'services.add', 'method'=>'POST', 'files'=>'true')) !!}
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
                            {!! Form::label('service_name', 'Service Name:') !!}
                            <div class="">
                            {!! Form::text('service_name', null, ['class'=>'form-control']) !!}
                            {!! $errors->first('service_name','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('duration', 'Duration:') !!}
                            <div class="">
                                {!! Form::text('duration', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('duration','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('amount', 'Amount:') !!}
                            <div class="">
                                {!! Form::text('amount', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('amount','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-1 text-center">&nbsp;<br/>
                        {!! Form::submit('Add Service', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">My Services:</div>
            <div class="panel-body">
                <ul>
                @foreach($services as $service)
                    <li>{{$service->service_name}}</li>
                @endforeach
                </ul>
            </div>

        </div>
    </div>
@endsection
