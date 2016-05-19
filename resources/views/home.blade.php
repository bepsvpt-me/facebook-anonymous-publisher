@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <header>
        <h1 class="text-center">靠北中正</h1>
      </header>

      <section>
        {!! Form::open(['route' => 'kobe', 'method' => 'POST', 'role' => 'form', 'data-toggle' => 'validator']) !!}
        <div class="form-group">
          {!! Form::textarea('content', null, ['class' => 'form-control', 'placeholder' => '今天要靠北什麼？', 'data-error' => '至少需要靠北點東西', 'required']) !!}
          <div class="help-block with-errors"></div>
        </div>

        <div class="form-group">
          {!! Recaptcha::render() !!}
        </div>

        {{--<div class="form-group">--}}
          {{--<div class="checkbox">--}}
            {{--<label>--}}
              {{--{!! Form::checkbox('accept-license', true, null, ['data-error' => '您必須同意本站隱私條款', 'required']) !!}--}}
              {{--<span>我同意並已詳細閱讀使用條款及本站隱私權政策</span>--}}
            {{--</label>--}}
          {{--</div>--}}
        {{--</div>--}}

        <div class="form-group">
          <button type="submit" class="btn btn-success btn-block">送出</button>
        </div>
        {!! Form::close() !!}

        @if (count($errors) > 0)
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
      </section>
    </div>
  </div>
@endsection
