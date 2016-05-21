@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <header>
        <h1 class="text-center">{{ $pageName }}</h1>
      </header>

      <section>
        <div class="panel panel-success">
          <div class="panel-heading">發文教學</div>
          <div class="panel-body">
            <ul>
              <li>當文章中有連結時，系統會用第一個連結當作欲分享的連結</li>
              <li>當文章中出現{{ $pageName }}的 hashtag 時，系統會自動在後方附上連結</li>
              <li>專案開源於 <a href="https://github.com/BePsvPT/kobeccu" target="_blank">Github</a></li>
            </ul>
          </div>
        </div>
      </section>

      <section>
        @include('components.form-errors')

        {!! Form::open(['route' => 'kobe', 'method' => 'POST', 'files' => true, 'role' => 'form', 'data-toggle' => 'validator']) !!}

        <div class="form-group">
          {!! Form::textarea('content', null, ['class' => 'form-control', 'placeholder' => '今天要靠北什麼？', 'maxlength' => 500, 'data-error' => '至少需要靠北點東西', 'required']) !!}
          <div class="help-block with-errors"></div>
        </div>

        <div class="row">
          <div class="col-xs-12 col-md-6">
            <div class="form-group">
              {!! Form::label('image', '圖片（可選）') !!}
              {!! Form::file('image', ['accept' => 'image/*']) !!}
              <p class="help-block">大小需小於 3 MB</p>
            </div>

            <div class="form-group">
              {!! Recaptcha::render() !!}
            </div>
          </div>

          <div class="col-xs-12 col-md-6">
            @unless(empty($application['ad-client']) || empty($application['ad-slot']))
              <section>
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="{{ $application['ad-client'] }}"
                     data-ad-slot="{{ $application['ad-slot'] }}"
                     data-ad-format="auto"></ins>
                <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
              </section>
            @endunless
          </div>
        </div>

        <div class="form-group {{ empty($application['license']) ? 'hidden' : '' }}">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('accept-license', true, empty($application['license']) ? true : false, ['data-error' => '您必須同意本站隱私條款', 'required']) !!}
              <span>我同意並已詳細閱讀使用條款及本站隱私權政策</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-success btn-block">送出</button>
        </div>

        {!! Form::close() !!}
      </section>

      @unless(empty($application['ga']))
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
          ga('create', '{{ $application['ga'] }}', 'auto');
          ga('send', 'pageview');
        </script>
      @endunless
    </div>
  </div>
@endsection
