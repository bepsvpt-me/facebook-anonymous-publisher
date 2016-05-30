@extends('layouts.master')

@section('main')
  <section>
    <h1 class="text-center">關鍵字封鎖</h1>
  </section>

  <section class="text-center">
    {{ Form::open(['route' => 'dashboard.block-words.store', 'method' => 'POST', 'class' => 'form-inline', 'role' => 'form', 'data-toggle' => 'validator']) }}

    <div class="form-group">
      {{ Form::label('value', '關鍵字', ['class' => 'sr-only']) }}
      {{ Form::text('value', null, ['class' => 'form-control', 'placeholder' => '關鍵字', 'required']) }}
    </div>

    {{ Form::submitButton('新增') }}

    {{ Form::close() }}
  </section>

  {{ Html::formErrors() }}

  <section>
    <table class="table table-bordered table-hover text-center table-middle">
      <thead>
        <tr>
          <th>關鍵字</th>
          <th>刪除</th>
        </tr>
      </thead>

      <tbody>
        @foreach($words as $word)
          <tr>
            <td>{{ $word }}</td>
            <td>{{ Html::linkButton('dashboard.block-words.delete', ['value' => $word], 'btn-danger', 'trash') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
