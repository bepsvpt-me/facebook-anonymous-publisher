@extends('layouts.master')

@inject('agent', 'Jenssegers\Agent\Agent')

@section('main')
  <section>
    <h1 class="text-center">發文列表</h1>
  </section>

  @include('components.pagination', ['pagination' => $posts])

  <section class="table-responsive">
    <table class="table table-bordered table-hover text-center table-middle">
      <thead>
        <tr>
          <th>編號</th>
          <th class="post-content-th">內容</th>
          <th>連結</th>
          <th>資訊</th>
          <th>封鎖</th>
          <th>刪除</th>
        </tr>
      </thead>

      <tbody>
        @foreach($posts as $post)
          @php($agent->setUserAgent($post->getAttribute('user_agent')))

          <tr>
            <td>{{ $post->getAttribute('id') }}</td>
            <td>
              <pre class="post-content">{{ $post->getAttribute('content') }}</pre>
            </td>
            <td>
              @if($post->trashed())
                <span>-</span>
              @elseif(is_null($post->getAttribute('fbid')))
                <span>尚未發布</span>
              @else
                <a href="https://www.facebook.com/{{ $post->getAttribute('fbid') }}" target="_blank">
                  <i class="fa fa-link" aria-hidden="true"></i>
                </a>
              @endif
            </td>
            <td>
              <p title="{{ $post->getAttribute('created_at') }}">提交於 {{ $post->getAttribute('created_at')->diffForHumans(\Carbon\Carbon::now()) }}</p>
              <p>來源 {{ $post->getAttribute('ip') }}</p>
              <p>{{ $agent->browser().' on '.$agent->platform() }}</p>
            </td>
            <td>
              <a href="{{ route('dashboard.posts.block', ['id' => $post->getKey()]) }}">
                <button type="button" class="btn btn-warning"><i class="fa fa-ban" aria-hidden="true"></i></button>
              </a>
            </td>
            <td>
              @if($post->trashed())
                <span>-</span>
              @else
                <a href="{{ route('dashboard.posts.delete', ['id' => $post->getKey()]) }}">
                  <button type="button" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>

  @include('components.pagination', ['pagination' => $posts])
@endsection
