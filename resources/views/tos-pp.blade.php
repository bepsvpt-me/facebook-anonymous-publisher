@extends('layouts.master')

@section('main')
  <section class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">{{ trans('kobe.terms-of-service') }}</h3>
    </div>
    <div id="terms-of-service" class="panel-body"></div>
  </section>

  <section class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">{{ trans('kobe.privacy-policy') }}</h3>
    </div>
    <div id="privacy-policy" class="panel-body"></div>
  </section>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/markdown.js/0.5.0/markdown.min.js"></script>
  <script>
    document.querySelector('#terms-of-service').innerHTML = markdown.toHTML(`{{ $application['terms_of_service'] }}`);
    document.querySelector('#privacy-policy').innerHTML = markdown.toHTML(`{{ $application['privacy_policy'] }}`);
  </script>
@endpush
