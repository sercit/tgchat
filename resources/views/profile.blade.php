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
                            {!! Form::label('phone', 'Телефон:') !!}
                            <div class="">
                                {!! Form::text('phone',$user->phone, ['class'=>'form-control']) !!}
                                {!! $errors->first('phone','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    @php $schedule = json_decode($user->schedule);@endphp
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_monday_begin__hours', 'Понедельника C:') !!}
                            <div class="">
                                {!! Form::select('schedule_monday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Monday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_monday_begin_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_monday_begin_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Monday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_monday_begin_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_monday_end_hours', 'Понедельник По:') !!}
                            <div class="">
                                {!! Form::select('schedule_monday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Monday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_monday_end_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_monday_end_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Monday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_monday_end_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_tuesday_begin_hours', 'Вторник C:') !!}
                            <div class="">
                                {!! Form::select('schedule_tuesday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Tuesday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_tuesday_begin_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_tuesday_begin_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Tuesday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_tuesday_begin_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_tuesday_end_hours', 'Вторник По:') !!}
                            <div class="">
                                {!! Form::select('schedule_tuesday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Tuesday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_tuesday_end_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_tuesday_end_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Tuesday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_tuesday_end_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_wednesday_begin_hours', 'Среда C:') !!}
                            <div class="">
                                {!! Form::select('schedule_wednesday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Wednesday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_wednesday_begin_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_wednesday_begin_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Wednesday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_wednesday_begin_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_wednesday_end_hours', 'Среда По:') !!}
                            <div class="">
                                {!! Form::select('schedule_wednesday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Wednesday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_wednesday_end_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_wednesday_end_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Wednesday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_wednesday_end_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_thursday_begin_hours', 'Четверг C:') !!}
                            <div class="">
                                {!! Form::select('schedule_thursday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Thursday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_thursday_begin_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_thursday_begin_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Thursday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_thursday_begin_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_thursday_end_hours', 'Четверг По:') !!}
                            <div class="">
                                {!! Form::select('schedule_thursday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Thursday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_thursday_end_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_thursday_end_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Thursday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_thursday_end_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_friday_begin_hours', 'Пятница C:') !!}
                            <div class="">
                                {!! Form::select('schedule_friday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Friday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_friday_begin_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_friday_begin_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Friday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_friday_begin_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_friday_end_hours', 'Пятница По:') !!}
                            <div class="">
                                {!! Form::select('schedule_friday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Friday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_friday_end_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_friday_end_minutes', array('00'=>'00','15'=>'30','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Friday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_friday_end_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_saturday_begin_hours', 'Суббота C:') !!}
                            <div class="">
                                {!! Form::select('schedule_saturday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Saturday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_saturday_begin_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_saturday_begin_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Saturday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_saturday_begin_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_saturday_end_hours', 'Суббота По:') !!}
                            <div class="">
                                {!! Form::select('schedule_saturday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Saturday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_saturday_end_hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_saturday_end_minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Saturday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_saturday_end_minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-group">
                            {!! Form::label('schedule_sunday_begin_hours', 'Воскресенье C:') !!}
                            <div class="">
                                {!! Form::select('schedule_sunday_begin_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Sunday)[0])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_sunday_begin__hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_sunday_begin__minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Sunday)[0])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_sunday_begin__minutes','<p class="alert alert-danger">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('schedule_sunday_end_hours', 'Воскресенье По:') !!}
                            <div class="">
                                {!! Form::select('schedule_sunday_end_hours', array('00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'), explode(':',explode('-',$schedule->Sunday)[1])[0],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_sunday_end__hours','<p class="alert alert-danger">:message</p>') !!}
                                {!! Form::select('schedule_sunday_end__minutes', array('00'=>'00','15'=>'15','30'=>'30','45'=>'45'), explode(':',explode('-',$schedule->Sunday)[1])[1],['class'=>'form-control']); !!}
                                {!! $errors->first('schedule_sunday_end__minutes','<p class="alert alert-danger">:message</p>') !!}
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

                    <div class="col-xs-12">
                        <div class="form-group">
                            <label for="id">Введите код в Telegram:</label>
                            <div class="">
                                <p>{!! $user->telegram_user_token !!}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 text-center">&nbsp;<br/>
                        {!! Form::submit('Сохранить', ['class'=>'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="panel panel-primary">

        </div>
    </div>
@endsection
