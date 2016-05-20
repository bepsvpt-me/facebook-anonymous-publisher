<div class="form-group">
  {!! Form::label('username', '管理員帳號') !!}
  {!! Form::text('username', null, ['class' => 'form-control', 'maxlength' => '24', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('password', '管理員密碼') !!}
  {!! Form::password('password', ['class' => 'form-control', 'data-minlength' => '6', 'required']) !!}
  <div class="help-block">最少需六個字</div>
</div>

<div class="form-group">
  {!! Form::label('page_name', '專頁名稱') !!}
  {!! Form::text('page_name', null, ['class' => 'form-control', 'required']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('extra_content', '自定義訊息') !!}
  {!! Form::textarea('extra_content', null, ['class' => 'form-control', 'rows' => 5]) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('license', '服務條款及隱私政策') !!}
  {!! Form::textarea('license', null, ['class' => 'form-control']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('ga', 'Google Analytics Code') !!}
  {!! Form::textarea('ga', null, ['class' => 'form-control', 'rows' => 3]) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('ad', 'Google AdSense Code') !!}
  {!! Form::textarea('ad', null, ['class' => 'form-control', 'rows' => 3]) !!}
  <div class="help-block with-errors"></div>
</div>
