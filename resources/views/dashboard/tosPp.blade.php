@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <section>
        {{ Html::formErrors() }}

        {{ Form::model($application, ['route' => 'dashboard.tos-pp.update', 'method' => 'POST', 'role' => 'form']) }}

        <div class="form-group">
          {{ Form::label('terms_of_service', '服務條款') }}
          {{ Form::textarea('terms_of_service', null, ['class' => 'form-control']) }}
        </div>

        <div class="form-group">
          {{ Form::label('privacy_policy', '隱私政策') }}
          {{ Form::textarea('privacy_policy', null, ['class' => 'form-control']) }}
        </div>

        {{ Form::submitButton('更新') }}

        {{ Form::close() }}
      </section>
    </div>
  </div>
@endsection
