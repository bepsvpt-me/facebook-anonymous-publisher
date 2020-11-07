@extends('layouts.master')

@inject('agent', 'Jenssegers\Agent\Agent')
@inject('now', 'Carbon\Carbon')

@section('main')
  <section>
    <h1 class="text-center">發文列表</h1>
  </section>

  {{ Html::pagination($posts) }}

  <section class="table-responsive">
    <table class="table table-bordered table-hover text-center table-middle">
      <thead>
        <tr>
          <th>編號</th>
          <th style="width: 60%;">內容</th>
          <th>資訊</th>
          <th>連結</th>
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
              <p title="{{ $post->getAttribute('created_at') }}">提交於 {{ $post->getAttribute('created_at')->diffForHumans($now) }}</p>

              @if(Auth::user()->own('admin'))
                <p>來源 {{ $post->getAttribute('ip') }}</p>
              @endif

              <p>{{ $agent->browser().' on '.$agent->platform() }}</p>
            </td>
            <td>
              @if($post->trashed())
                <span>-</span>
              @elseif(is_null($post->getAttribute('fbid')))
                <span>尚未發布</span>
              @else
                <a href="https://www.facebook.com/{{ $post->getAttribute('fbid') }}" target="_blank">{{ Html::icon('link') }}</a>
              @endif
            </td>
            <td>{{ Html::linkButton('dashboard.posts.block', ['id' => $post->getKey()], 'btn-warning', 'ban') }}</td>
            <td>
              @if($post->trashed())
                <span>-</span>
              @else
                {{ Html::linkButton('dashboard.posts.delete', ['id' => $post->getKey()], 'btn-danger', 'trash') }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>

  {{ Html::pagination($posts) }}
@endsection
