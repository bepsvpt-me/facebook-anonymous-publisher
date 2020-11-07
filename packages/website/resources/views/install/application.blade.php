<div class="form-group">
  {{ Form::requiredHint() }}
  {{ Form::label('username', '管理員帳號') }}
  {{ Form::text('username', null, ['class' => 'form-control', 'maxlength' => '24', 'required']) }}
  {{ Form::validatorHelper() }}
</div>

<div class="form-group">
  {{ Form::requiredHint() }}
  {{ Form::label('password', '管理員密碼') }}
  {{ Form::password('password', ['class' => 'form-control', 'data-minlength' => '6', 'required']) }}
  <div class="help-block">最少需六個字</div>
</div>

<div class="form-group">
  {{ Form::requiredHint() }}
  {{ Form::label('page_name', '專頁名稱') }}
  {{ Form::text('page_name', null, ['class' => 'form-control', 'required']) }}
  {{ Form::validatorHelper() }}
</div>
