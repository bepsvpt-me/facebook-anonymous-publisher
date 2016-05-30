<a href="{{ route($name, $parameters) }}">
  <button type="button" class="btn {{ $buttonClass }}">
    @if(! is_null($icon))
      {{ Html::icon($icon) }}
    @endif

    @if(! is_null($text))
      <span>{{ $text }}</span>
    @endif
  </button>
</a>
