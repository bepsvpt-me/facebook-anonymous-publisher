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
            <li>以上為常見被封鎖原因，如您為共用ＩＰ之使用者，只要其中一位住戶違反相關規定，將會連累不能使用，請特別注意</li>
            <li>如您的ＩＰ位址被封鎖，則可以透過登入的方式解除此限制，請特別注意，<strong class="text-danger">此時發文即不是匿名，如果您想要匿名發言，又不想任何負責，很抱歉，此系統無法滿足您的需求</strong>，在其餘狀況下，只要您的ＩＰ位址未被封鎖，不管是否有登入，本系統皆不會紀錄足以辨別使用者之資訊</li>
          </ol>
        </section>
      @else
        <section>
          {{ Html::formErrors() }}

          {{ Form::open(['route' => 'kobe', 'method' => 'POST', 'files' => true, 'role' => 'form', 'data-toggle' => 'validator']) }}

          <div class="form-group">
            {{ Form::textarea('content', null, ['class' => 'form-control', 'placeholder' => '今天要靠北什麼？', 'maxlength' => 500, 'data-error' => '至少需要靠北點東西', 'required']) }}
            {{ Form::validatorHelper() }}
          </div>

          <div class="form-group">
            <div class="checkbox">
              <label>
                {{ Form::checkbox('post-by-image', true, false, ['id' => 'post-by-image']) }}
                <span>使用文字圖片</span>
              </label>

              {{ Form::hidden('color', '000000', ['id' => 'post-image-color']) }}
              <button class="jscolor {valueElement: 'post-image-color'}">背景顏色</button>
            </div>
          </div>

          <div id="post-image" class="form-group">
            {{ Form::label('image', '圖片（可選）') }}
            {{ Form::file('image', ['accept' => 'image/*']) }}
            <p class="help-block">大小需小於 3 MB</p>
          </div>

          <div class="form-group">
            {!! Recaptcha::render() !!}
          </div>

          <div class="form-group">
            <div class="panel panel-info">
              <div class="panel-heading">
                <h3 class="panel-title">服務條款及隱私政策</h3>
              </div>
              <div class="panel-body">
                <ol>
                  <li>嚴禁發表任何違反中華民國法律之內容</li>
                  <li>嚴禁發表任何違反新加坡法律之內容</li>
                  <li>嚴禁發表任何違反 Facebook 社群使用規則之內容</li>
                  <li>嚴禁指名道姓、透漏任何個資或隱私資訊，請善用「x」取代敏感資訊，取代程度須達到不足以辨別當事者</li>
                  <li>嚴禁發表政治文以及非靠北文</li>
                  <li>本網站是以即時上載發文的方式運作，對所有發文的真實性、完整性及立場等，不負任何法律責任</li>
                  <li>本網站受到「即時上載發文」運作方式所規限，故不能完全監察所有發文，若讀者發現有發文出現問題，請至粉絲專頁聯絡我們</li>
                  <li>本網站有權刪除任何發文及拒絕任何人士上載發文，同時亦有不刪除發文的權利</li>
                  <li>本網站保留一切法律權利</li>
                </ol>

                {{--<pre>{{ $application['license'] }}</pre>--}}

                <div class="checkbox">
                  <label>
                    {{ Form::checkbox('accept-license', true, null, ['data-error' => '您必須同意本站隱私條款', 'required']) }}
                    <span>我同意並已詳細閱讀服務條款及隱私政策，並同意於按下送出按鈕後放棄對本網站所有法律追訴權</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          {{ Form::submitButton('送出') }}

          {{ Form::close() }}
        </section>
      @endif

      @unless(empty($application['ad-client']) || empty($application['ad-slot']))
        <section>
          <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
          <ins class="adsbygoogle block"
               data-ad-client="{{ $application['ad-client'] }}"
               data-ad-slot="{{ $application['ad-slot'] }}"
               data-ad-format="auto"></ins>
          <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
        </section>
      @endunless
    </div>
  </div>
@endsection

@push('scripts')
  {{ Html::script('https://cdnjs.cloudflare.com/ajax/libs/jscolor/2.0.4/jscolor.min.js') }}

  <script>
    $(document).on('submit', 'form', function () {
      $('button.btn-success').attr('disabled', true);
    });

    $(document).on('change', '#post-by-image', function () {
      $("#post-image").toggle(! this.checked);
    });
  </script>
@endpush
