@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Services</div>
            <div class="panel-body">
                {!! Form::open(array('route' => 'clients.add', 'method'=>'POST', 'files'=>'true')) !!}
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
                            {!! Form::label('client_name', 'Client Name:') !!}
                            <div class="">
                            {!! Form::text('client_name', null, ['class'=>'form-control']) !!}
                            {!! $errors->first('client_name','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('phone', 'Phone:') !!}
                            <div class="">
                                {!! Form::text('phone', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('phone','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-1 text-center">&nbsp;<br/>
                        {!! Form::submit('Add Client', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">My Clients:</div>
            <div class="panel-body">
                <ul>
                @foreach($clients as $client)
                        <li><a href="/clients/{{$client->id}}">{{$client->client_name}}</a> - <a href="tel:{{$client->phone}}">{{$client->phone}}</a></li>
                @endforeach
                </ul>
            </div>

        </div>
    </div>
@endsection
