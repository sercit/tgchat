@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Заказы</div>
            <div class="panel-body">
                {!! Form::open(array('route' => 'orders.add', 'method'=>'POST', 'files'=>'true')) !!}
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
                            {!! Form::label('service_id', 'Услуга:') !!}
                            <div class="">
                                {!! Form::select('service_id', $services,null, ['class'=>'form-control']) !!}
                                {!! $errors->first('service_id','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                            {!! Form::label('name', 'Имя:') !!}
                            <div class="">
                                {!! Form::text('name', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('name','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('phone', 'Телефон:') !!}
                            <div class="">
                                {!! Form::text('phone', null, ['class'=>'form-control']) !!}
                                {!! $errors->first('phone','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            {!! Form::label('date', 'Дата:') !!}
                            <div class="">
                                {!! Form::text('date', null, ['class'=>'form-control','autocomplete'=>'off']) !!}
                                {!! $errors->first('date','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-1 text-center">&nbsp;<br/>
                        {!! Form::submit('Добавить заказ', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">My Orders:</div>
            <div class="panel-body">
                <div class="row">
                @foreach($orders as $order)
                    {!! Form::open(array('method'=>'POST', 'files'=>'true')) !!}
                    <div class="col-xs-4 card" style="width: 18rem;">
                        <div class="card-body">
                            <h5 class="card-title">{{$order->service_name}} - {{$order->id}}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">{{$order->duration}}</h6>
                            <h6 class="card-subtitle mb-2 text-muted">{{$order->amount}}Р</h6>
                            <h6 class="card-subtitle mb-2 text-muted">{{$order->name}}</h6>
                            <h6 class="card-subtitle mb-2 text-muted">{{$order->phone}}</h6>
                            <h6 class="card-subtitle mb-2 text-muted">{{$order->date}}</h6>
                            <a href="{{route('orders.confirm', $order->id)}}" class="card-link">Подтвердить</a>
                            <a href="{{route('orders.cancel', $order->id)}}" class="card-link">Отменить</a>
                        </div>
                    </div>
                        {!! Form::close() !!}
                @endforeach
                </div>
            </div>

        </div>
    </div>

@endsection
@section('pageScript')
    <link rel="stylesheet" type="text/css" href="js/jquery.datetimepicker.css"/>
    <script src="js/build/jquery.datetimepicker.full.js"></script>
    <script>
        $('#date').datetimepicker({
            format:'Y-m-d H:i:00',
            lang:'ru',
            step:15,
            minDate:0,
        });
    </script>
@endsection

