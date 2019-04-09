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
                            {!! Form::label('email', 'Email:') !!}
                            <div class="">
                            {!! Form::text('email',$user->email, ['class'=>'form-control']) !!}
                            {!! $errors->first('email','<p class="alert alert-danger">:message</p>') !!}
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
