@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('Новая ссылка для верификации была отправлена на ваш E-mail.') }}
                        </div>
                    @endif

                    {{ __('Проверьте, пожалуйста, ваш E-mail, мы отправили на него ссылку для верификации.') }}
                    {{ __('Если вы не получили письмо ') }}, <a href="{{ route('verification.resend') }}">{{ __('нажмите здесь для отпрвки нового') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
