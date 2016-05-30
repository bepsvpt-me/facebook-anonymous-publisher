@extends('layouts.master')

@section('main')
  <section class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">{{ trans('kobe.terms-of-service') }}</h3>
    </div>
    <div class="panel-body">
      {{ Html::ol(explode(PHP_EOL, $application['terms_of_service'] ?? '')) }}
    </div>
  </section>

  <section class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">隱私政策</h3>
    </div>
    <div class="panel-body">
      <p>感謝您蒞臨{{ $application['page_name'] }}(以下簡稱本網站)，關於您的個人隱私權，本網站絕對尊重並予以保護。為了讓您能夠更安心的使用本網站所提供之各項服務，特於此向您說明本網站的隱私權保護政策。</p>
      {{ Html::ol(explode(PHP_EOL, $application['privacy_policy'] ?? '')) }}
    </div>
  </section>
@endsection
