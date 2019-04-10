@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Service {{$service->id}}</div>
            <div class="panel-body">
                {!! Form::open(array('route' => array('services.edit', $service->id), 'method'=>'POST', 'files'=>'true')) !!}
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

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="id">id</label>
                            <div class="">
                                <p>{!! $service->id !!}</p>
                                {!! Form::hidden('id',$service->id) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('service_name', 'Service Name:') !!}
                            <div class="">
                                {!! Form::text('service_name',$service->service_name, ['class'=>'form-control']) !!}
                                {!! $errors->first('service_name','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('duration', 'Duration:') !!}
                            <div class="">
                                {!! Form::text('duration',$service->duration, ['class'=>'form-control']) !!}
                                {!! $errors->first('duration','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('amount', 'Amount:') !!}
                            <div class="">
                                {!! Form::text('amount',$service->amount, ['class'=>'form-control']) !!}
                                {!! $errors->first('amount','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-center">&nbsp;<br/>
                        {!! Form::submit('Sumbit changes', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="row">
                {!! Form::open(array('route' => array('services.destroy', $service->id), 'method'=>'DELETE', 'files'=>'true')) !!}
                    <div class="col-xs-12 text-center">&nbsp;<br/>
                        {!! Form::submit('Delete service', ['class'=>'btn btn-danger']) !!}
                    </div>
                {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
