<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <a class="navbar-brand" href="{{ route('home') }}">{{ Html::icon('bullhorn', true) }} {{ $application['page_name'] }}</a>
    </div>

    <div class="collapse navbar-collapse" id="navbar-collapse">
      <ul class="nav navbar-nav navbar-right">
        @if(Auth::guest())
          <li><a href="{{ route('oauth.facebook') }}">{{ Html::icon('facebook-official', true) }} 登入</a></li>
        @else
          <li><a href="{{ route('auth.sign-out') }}">{{ Html::icon('sign-out', true) }} 登出</a></li>
        @endif

        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <span>{{ Html::icon('line-chart', true) }} {{ trans('kobe.navbar.ranking.title') }} </span><span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <li>{{ Html::linkRoute('ranking.daily', trans('kobe.navbar.ranking.daily')) }}</li>
            <li>{{ Html::linkRoute('ranking.weekly', trans('kobe.navbar.ranking.weekly')) }}</li>
            <li>{{ Html::linkRoute('ranking.monthly', trans('kobe.navbar.ranking.monthly')) }}</li>
          </ul>
        </li>

        @if(Auth::check() && Auth::user()->is('manager'))
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span>{{ Html::icon('tachometer', true) }} {{ trans('kobe.navbar.dashboard.title') }} </span><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li>{{ Html::linkRoute('dashboard.posts.index', trans('kobe.navbar.dashboard.posts')) }}</li>
              <li>{{ Html::linkRoute('dashboard.block-words.index', trans('kobe.navbar.dashboard.block-words')) }}</li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>
