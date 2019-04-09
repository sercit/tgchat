@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Client {{$client->id}}</div>
            <div class="panel-body">
                <p>Name - {{$client->client_name}}</p>
                <p>Phone - {{$client->phone}}</p>
            </div>
        </div>
    </div>
@endsection
