<div class="form-group">
  {{ Form::requiredHint() }}
  {{ Form::label('public_key', 'Site Key') }}
  {{ Form::text('public_key', null, ['class' => 'form-control', 'required']) }}
  {{ Form::validatorHelper() }}
</div>

<div class="form-group">
  {{ Form::requiredHint() }}
  {{ Form::label('private_key', 'Secret Key') }}
  {{ Form::password('private_key', ['class' => 'form-control', 'required']) }}
  {{ Form::validatorHelper() }}
</div>
