<div class="form-group">
  {!! Form::label('public_key', 'Site Key') !!}
  {!! Form::text('public_key', null, ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('private_key', 'Secret Key') !!}
  {!! Form::password('private_key', ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>
