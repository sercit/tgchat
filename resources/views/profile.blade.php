@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading">Profile</div>
            <div class="panel-body">
                {!! Form::open(array('route' => 'profile.edit', 'method'=>'POST', 'files'=>'true')) !!}
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
                                <p>{!! $user->id !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('lastname', 'Фамилия:') !!}
                            <div class="">
                                {!! Form::text('lastname',$user->lastname, ['class'=>'form-control']) !!}
                                {!! $errors->first('lastname','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('firstname', 'Имя:') !!}
                            <div class="">
                            {!! Form::text('firstname',$user->firstname, ['class'=>'form-control']) !!}
                            {!! $errors->first('firstname','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('patronymic', 'Отчество:') !!}
                            <div class="">
                                {!! Form::text('patronymic',$user->patronymic, ['class'=>'form-control']) !!}
                                {!! $errors->first('patronymic','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('address', 'Адрес:') !!}
                            <div class="">
                                {!! Form::text('address',$user->address, ['class'=>'form-control']) !!}
                                {!! $errors->first('address','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('welcome_message', 'Приветственное сообщение:') !!}
                            <div class="">
                                {!! Form::text('welcome_message',$user->welcome_message, ['class'=>'form-control']) !!}
                                {!! $errors->first('welcome_message','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule', 'Расписание:') !!}
                            <div class="">
                                {!! Form::text('schedule',$user->schedule, ['class'=>'form-control']) !!}
                                {!! $errors->first('schedule','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('email', 'Email:') !!}
                            <div class="">
                                {!! Form::text('email',$user->email, ['class'=>'form-control']) !!}
                                {!! $errors->first('email','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="id">Оплачено до:</label>
                            <div class="">
                                <p>{!! $user->paid_until !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-center">&nbsp;<br/>
                        {!! Form::submit('Sumbit changes', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">

        </div>
    </div>
@endsection
