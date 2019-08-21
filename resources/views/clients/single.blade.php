@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Клиент #{{$client->id}}</div>
            <div class="panel-body">
                {!! Form::open(array('route' => array('clients.edit', $client->id), 'method'=>'POST', 'files'=>'true')) !!}
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
                                <p>{!! $client->id !!}</p>
                                {!! Form::hidden('id',$client->id) !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('client_name', 'Имя клиента:') !!}
                            <div class="">
                                {!! Form::text('client_name',$client->client_name, ['class'=>'form-control']) !!}
                                {!! $errors->first('client_name','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('phone', 'Телефон:') !!}
                            <div class="">
                                {!! Form::text('phone',$client->phone, ['class'=>'form-control']) !!}
                                {!! $errors->first('phone','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-center">&nbsp;<br/>
                        {!! Form::submit('Изменить', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
                <div class="row">
                    {!! Form::open(array('route' => array('clients.destroy', $client->id), 'method'=>'DELETE', 'files'=>'true')) !!}
                    <div class="col-xs-12 text-center">&nbsp;<br/>
                        {!! Form::submit('Удалить клиента', ['class'=>'btn btn-danger']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
