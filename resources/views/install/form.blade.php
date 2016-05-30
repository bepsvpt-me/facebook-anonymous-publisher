@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <header>
        <h1 class="text-center">{{ ucfirst($service) }} {{ trans('kobe.install.service-setting') }}</h1>
      </header>

      <section>
        {{ Html::formErrors() }}

        {{ Form::model(\App\Config::getConfig("$service-service"), ['route' => "install.{$service}.store", 'method' => 'POST', 'role' => 'form', 'data-toggle' => 'validator']) }}

        @include("install.{$service}")

        {{ Form::submitButton(trans('kobe.install.next-step')) }}

        {{ Form::close() }}
      </section>
    </div>
  </div>
@endsection
