@extends('layouts.master')

@section('main')
  <div class="panel panel-info">
    <div class="panel-heading">重新安裝</div>
    <div class="panel-body">
      <h4>如須重新設定 Facebook、Recaptcha 等相關設定，請點擊下方按鈕，此操作不會清除已存在的資料</h4>

      <a href="{{ route('dashboard.website.reset') }}">
        <button type="button" class="btn btn-danger">重新安裝</button>
      </a>
    </div>
  </div>
@endsection
