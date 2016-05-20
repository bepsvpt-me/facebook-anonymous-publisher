@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <header>
        <h1 class="text-center">{{ ucfirst($service) }} 服務設定</h1>
      </header>

      <section>
        @include('components.form-errors')

        {!! Form::model(\App\Config::getConfig("$service-service"), ['route' => "install.{$service}.store", 'method' => 'POST', 'role' => 'form', 'data-toggle' => 'validator']) !!}

        @include("install.{$service}")

        @include('components.submitButton', ['text' => '下一步'])

        {!! Form::close() !!}
      </section>
    </div>
  </div>
@endsection
