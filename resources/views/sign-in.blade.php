@extends('layouts.master')

@section('main')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-info">
                <div class="panel-heading">登入</div>

                <div class="panel-body">
                  {!! Form::open(['route' => 'auth.auth', 'method' => 'POST', 'role' => 'form', 'data-toggle' => 'validator']) !!}
                  <div class="form-group">
                    {!! Form::label('username', 'Username') !!}
                    {!! Form::text('username', null, ['class' => 'form-control', 'required']) !!}
                    <div class="help-block with-errors"></div>
                  </div>

                  <div class="form-group">
                    {!! Form::label('password', 'Password') !!}
                    {!! Form::password('password', ['class' => 'form-control', 'required']) !!}
                    <div class="help-block with-errors"></div>
                  </div>

                  @include('components.submitButton', ['text' => '登入'])

                  {!! Form::close() !!}

                  @include('components.form-errors')
                </div>
            </div>
        </div>
    </div>
@endsection
