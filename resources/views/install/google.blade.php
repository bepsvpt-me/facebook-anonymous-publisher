<div class="form-group">
  {{ Form::label('ga', 'Google Analytics ID') }}
  {{ Form::text('ga', null, ['class' => 'form-control', 'placeholder' => 'UA-xxxxxxxx-x']) }}
</div>

<div class="form-group">
  {{ Form::label('ad-client', 'Google AdSense Client') }}
  {{ Form::text('ad-client', null, ['class' => 'form-control', 'placeholder' => 'ca-pub-xxxxxxxx']) }}
</div>

<div class="form-group">
  {{ Form::label('ad-slot', 'Google AdSense Slot') }}
  {{ Form::text('ad-slot', null, ['class' => 'form-control']) }}
</div>
