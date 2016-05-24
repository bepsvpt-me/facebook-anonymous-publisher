<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>

      <a class="navbar-brand" href="{{ route('home') }}">{{ $application['page_name'] }}</a>
    </div>

    <div class="collapse navbar-collapse" id="navbar-collapse">
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <span><i class="fa fa-line-chart fa-fw" aria-hidden="true"></i> {{ trans('kobe.navbar.ranking.title') }} </span><span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <li><a href="{{ route('ranking.daily') }}">{{ trans('kobe.navbar.ranking.daily') }}</a></li>
            <li><a href="{{ route('ranking.weekly') }}">{{ trans('kobe.navbar.ranking.weekly') }}</a></li>
            <li><a href="{{ route('ranking.monthly') }}">{{ trans('kobe.navbar.ranking.monthly') }}</a></li>
          </ul>
        </li>

        @if(Auth::check())
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
              <span><i class="fa fa-tachometer fa-fw" aria-hidden="true"></i> {{ trans('kobe.navbar.dashboard.title') }} </span><span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="{{ route('dashboard.posts.index') }}">{{ trans('kobe.navbar.dashboard.posts') }}</a></li>
              <li><a href="{{ route('dashboard.block-words.index') }}">{{ trans('kobe.navbar.dashboard.block-words') }}</a></li>
            </ul>
          </li>
        @endif
      </ul>
    </div>
  </div>
</nav>
