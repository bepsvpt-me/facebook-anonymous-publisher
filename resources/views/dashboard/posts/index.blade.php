@extends('layouts.master')

@inject('agent', 'Jenssegers\Agent\Agent')

@section('main')
  <section>
    <h1 class="text-center">文章列表</h1>
  </section>

  @include('components.pagination', ['pagination' => $posts])

  <section class="table-responsive">
    <table class="table table-bordered table-hover text-center table-middle">
      <thead>
        <tr>
          <th class="text-center">編號</th>
          <th class="text-center">內容</th>
          <th class="text-center">連結</th>
          <th class="text-center">資訊</th>
          <th class="text-center">刪除</th>
        </tr>
      </thead>

      <tbody>
        @foreach($posts as $post)
          @php($agent->setUserAgent($post->getAttribute('user_agent')))

          <tr>
            <td>{{ $post->getAttribute('id') }}</td>
            <td>
              <pre class="text-left">{{ $post->getAttribute('content') }}</pre>
            </td>
            <td>
              @if(! is_null($post->getAttribute('fbid')))
                <a href="https://www.facebook.com/{{ $post->getAttribute('fbid') }}" target="_blank">
                  <i class="fa fa-link" aria-hidden="true"></i>
                </a>
              @else
                <span>尚未發布</span>
              @endif
            </td>
            <td>
              <p title="{{ $post->getAttribute('created_at') }}">提交於 {{ $post->getAttribute('created_at')->diffForHumans(\Carbon\Carbon::now()) }}</p>
              <p>來源 {{ $post->getAttribute('ip') }}</p>
              <p>{{ $agent->browser().' on '.$agent->platform() }}</p>
            </td>
            <td>
              <a href="{{ route('dashboard.delete', ['id' => $post->getKey()]) }}">
                <button type="button" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>

  @include('components.pagination', ['pagination' => $posts])
@endsection

@push('styles')
  <style>
    table.table-middle td {
      vertical-align: middle !important;
    }
  </style>
@endpush
