@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8">
      <header>
        <h1 class="text-center">{{ $application['page_name'] }}</h1>
      </header>

      @if(is_block_ip())
        <section>
          <h3 class="text-center text-info">您的ＩＰ位址可能因下列原因而被系統封鎖，目前無法使用發文系統</h3>

          <ol>
            <li>過去發文紀錄中，有違反法律條文之紀錄</li>
            <li>過去發文紀錄中，有違反 Facebook 使用條款之紀錄</li>
            <li>過去發文紀錄中，有指名道姓、透漏任何個資或隱私資訊之紀錄</li>
            <li>過去發文紀錄中，有惡意洗版之行為</li>
            <li>過去發文紀錄中，有足以造成本專頁有法律風險之紀錄</li>
            <li>以上為常見被封鎖原因，如您為共用ＩＰ之使用者，只要其中一位用戶違反相關規定，將會連累不能使用，請特別注意</li>
            <li>如您的ＩＰ位址被封鎖，則可以透過登入的方式解除此限制，請特別注意，<strong class="text-danger">此時發文即不是匿名，如果您想要匿名發言，又不想任何負責，很抱歉，此系統無法滿足您的需求</strong>，在其餘狀況下，只要您的ＩＰ位址未被封鎖，不管是否有登入，本系統皆不會紀錄足以辨別使用者之資訊</li>
          </ol>
        </section>
      @elseif(! is_support_country())
        <section class="text-center">
          <h3 class="text-info">本服務不支援您所在的國家/地區</h3>

          <span>This product includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com" target="_blank">https://www.maxmind.com</a>.</span>
        </section>
      @else
        <section>
          {{ Html::formErrors() }}

          {{ Form::open(['route' => 'kobe', 'method' => 'POST', 'files' => true, 'role' => 'form', 'data-toggle' => 'validator']) }}

          <div class="form-group">
            {{ Form::textarea('content', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => '今天要靠北什麼？', 'maxlength' => 500, 'required']) }}
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
            {!! Recaptcha::render() !!}
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

      @unless(empty($application['ad-client']) || empty($application['ad-slot']))
        <section>
          <script src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js" defer></script><ins class="adsbygoogle block" data-ad-client="{{ $application['ad-client'] }}" data-ad-slot="{{ $application['ad-slot'] }}" data-ad-format="auto"></ins><script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </section>
      @endunless
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/3.0.15/autosize.min.js"></script>

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
