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
                            {!! Form::label('service_name', 'Имя услуги:') !!}
                            <div class="">
                            {!! Form::text('service_name', null, ['class'=>'form-control']) !!}
                            {!! $errors->first('service_name','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('duration', 'Продолжительность(в минутах):') !!}
                            <div class="">
                                {!! Form::text('duration', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('duration','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('amount', 'Стоимость:') !!}
                            <div class="">
                                {!! Form::text('amount', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('amount','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-1 text-center">&nbsp;<br/>
                        {!! Form::submit('Добавить Услугу', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">My Services:</div>
            <div class="panel-body">
                <div class="row">
                @foreach($services as $service)
                    <div class="col-xs-4 card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">{{$service->service_name}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{$service->duration}}</h6>
                            <h6 class="card-subtitle mb-2 text-muted">{{$service->amount}}Р</h6>
                            <a href="{{url ('/services/'.$service->id)}}" class="card-link">Редактировать</a>
                            <a href="{{url ('/services/'.$service->id).'/destroy'}}" class="card-link">Удалить</a>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>

        </div>
    </div>
@endsection
