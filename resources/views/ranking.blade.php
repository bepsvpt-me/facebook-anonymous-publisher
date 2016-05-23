@extends('layouts.master')

@section('main')
  <section>
    <div class="row">
      @foreach($posts as $post)
        <div class="col-xs-12 col-md-offset-3 col-md-6" style="padding: 25px 15px;">
          <div class="fb-post" data-href="https://www.facebook.com/{{ $post->getAttribute('fbid') }}"></div>
        </div>
      @endforeach
    </div>
  </section>

  @include('components.pagination', ['pagination' => $posts])
@endsection

@push('scripts')
  <script src="https://connect.facebook.net/zh_TW/sdk.js#xfbml=1&amp;version=v2.2" async></script>
@endpush
