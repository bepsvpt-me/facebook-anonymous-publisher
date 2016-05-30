@extends('layouts.master')

@section('main')
  <div class="row">
    <div class="col-xs-12 col-md-offset-2 col-md-8 text-center">
      <header>
        <h1 class="text-success">恭喜你已完成安裝</h1>
      </header>

      <br>

      <section class="lead">
        <p>現在起，安裝頁面將無法訪問，如欲更改相關設定，須先重置後再前往安裝頁面設定</p>

        <p>最後，感謝您使用本服務，祝您有個美好的一天</p>

        <br>

        {{ Html::linkRoute('home', '回首頁') }}
      </section>
    </div>
  </div>
@endsection
