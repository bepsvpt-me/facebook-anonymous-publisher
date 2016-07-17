@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <section>
        {{ Html::formErrors() }}

        {{ Form::model($application, ['route' => 'dashboard.page.update', 'method' => 'POST', 'role' => 'form']) }}

        <div class="form-group">
          {{ Form::requiredHint() }}
          {{ Form::label('page_name', '專頁名稱') }}
          {{ Form::text('page_name', null, ['class' => 'form-control', 'required']) }}
          {{ Form::validatorHelper() }}
        </div>

        <div class="form-group">
          {{ Form::label('extra_content', '發文自定義訊息') }}
          {{ Form::textarea('extra_content', null, ['class' => 'form-control', 'rows' => 5]) }}
          {{ Form::validatorHelper() }}
        </div>

        {{ Form::submitButton('更新') }}

        {{ Form::close() }}
      </section>
    </div>
  </div>
@endsection
