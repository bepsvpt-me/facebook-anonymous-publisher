@extends('layouts.master')

@section('main')
  <section class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">Changelog</h3>
    </div>
    <div id="changelog" class="panel-body">
## 1.x

- 1.0.1 (2016-06-08)
  - Fix incorrect form error message
  - Fix incorrect user role check
  - Support ipv6 if application is behind cloudflare
  - Support restrict user country

- 1.0.0 (2016-06-01)
  - Initial release
    </div>
  </section>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/0.3.5/marked.min.js"></script>

  <script>
    document.querySelector('#changelog').innerHTML = marked(document.querySelector('#changelog').innerHTML);
  </script>
@endpush
