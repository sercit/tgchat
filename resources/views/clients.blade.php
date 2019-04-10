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
                <div class="row">
                    @foreach($clients as $client)
                            <div class="col-xs-4 card" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title">{{$client->client_name}}</h5>
                                    <h6 class="card-subtitle mb-2 text-muted">{{$client->phone}}</h6>
                                    <a href="{{url ('/clients/'.$client->id)}}" class="card-link">Редактировать</a>
                                    <a href="#" class="card-link">Another link</a>
                              </div>
                            </div>
                    @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
