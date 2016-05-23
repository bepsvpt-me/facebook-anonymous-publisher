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
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">排行榜 <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="{{ route('ranking.daily') }}">日排行</a></li>
            <li><a href="{{ route('ranking.weekly') }}">週排行</a></li>
            <li><a href="{{ route('ranking.monthly') }}">月排行</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
