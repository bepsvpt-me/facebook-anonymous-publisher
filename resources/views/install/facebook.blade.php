<div class="form-group">
  {!! Form::label('app_id', 'App Id') !!}
  {!! Form::text('app_id', null, ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('app_secret', 'App Secret') !!}
  {!! Form::text('app_secret', null, ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('default_graph_version', 'Graph Api Version') !!}
  {!! Form::text('default_graph_version', null, ['class' => 'form-control', 'placeholder' => 'v2.6', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('default_access_token', 'Access Token') !!}
  {!! Form::text('default_access_token', null, ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>
