@extends('layouts.master')

@inject('firewall', 'FacebookAnonymousPublisher\Firewall\Firewall')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <header>
        <h1 class="text-center">{{ $application['page_name'] }}</h1>
      </header>

      @php($banned = $firewall->isBanned())

      @if('permanent' === $banned || (false !== $banned && Auth::guest()))
        <section>
          @if('permanent' !== $banned)
            <h3 class="text-center text-info">
              <p>您必須先登入方能使用發文系統</p>
              <a href="{{ route('oauth.facebook') }}">{{ trans('kobe.navbar.auth.sign-in') }}</a>
            </h3>
          @else
            <h3 class="text-center text-info">您無法使用發文系統</h3>
          @endif
        </section>
      @elseif(! $firewall->isAllowCountry())
        <section class="text-center">
          <h3 class="text-info">本服務不支援您所在的國家/地區</h3>

          <span>This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com" target="_blank">https://www.maxmind.com</a>.</span>
        </section>
      @else
        <section>
          {{ Html::formErrors() }}

          {{ Form::open(['route' => 'kobe', 'method' => 'POST', 'files' => true, 'role' => 'form', 'data-toggle' => 'validator']) }}

          <div class="form-group">
            {{ Form::textarea('content', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => '今天要靠北什麼？', 'maxlength' => 1500, 'required']) }}
            {{ Form::validatorHelper() }}
          </div>

          <div class="form-group">
            <div class="checkbox">
              <label>
                {{ Form::checkbox('post-by-image', true, false, ['id' => 'post-by-image']) }}
                <span>使用文字圖片</span>
              </label>

              {{ Form::hidden('color', '000000', ['id' => 'post-image-color']) }}
              <button class="jscolor {valueElement: 'post-image-color'}">底色</button>
            </div>
          </div>

          <div id="post-image" class="form-group">
            {{ Form::label('image', '圖片（可選，小於 3 MB）') }}
            {{ Form::file('image', ['accept' => 'image/*', 'style' => 'display: inline;']) }}
          </div>

          <div class="form-group">
            <div class="g-recaptcha" data-sitekey="{{ config('recaptcha.public_key') }}" ></div>
          </div>

          <div class="form-group">
            <div class="checkbox">
              <label>
                {{ Form::checkbox('accept-license', true, null, ['required']) }}
                <span>我同意並已詳細閱讀 {{ Html::linkRoute('tos-pp', '服務條款', [], ['target' => '_blank']) }} 及 {{ Html::linkRoute('tos-pp', '隱私政策', [], ['target' => '_blank']) }}</span>
              </label>
            </div>
          </div>

          {{ Form::submitButton('送出') }}

          {{ Form::close() }}
        </section>
      @endif

      @unless(empty($google['ad-client']) || empty($google['ad-slot']))
        <section>
          <script src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" defer></script><ins class="adsbygoogle block" data-ad-client="{{ $google['ad-client'] }}" data-ad-slot="{{ $google['ad-slot'] }}" data-ad-format="auto"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </section>
      @endunless
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/3.0.15/autosize.min.js"></script>
  <script src='https://www.google.com/recaptcha/api.js?render=onload' defer></script>

  <script>
    $('form').on('submit', function () {
      $('button.btn-success').attr('disabled', true);
    });

    $('#post-by-image').on('change', function () {
      $("#post-image").toggle(! this.checked);
    });

    autosize(document.querySelector('textarea'));
  </script>
@endpush
