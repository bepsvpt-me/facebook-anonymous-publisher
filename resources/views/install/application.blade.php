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
  {!! Form::label('ga', 'Google Analytics 編號') !!}
  {!! Form::text('ga', null, ['class' => 'form-control', 'placeholder' => 'UA-xxxxxxxx-x']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('ad-client', 'Google AdSense Client') !!}
  {!! Form::text('ad-client', null, ['class' => 'form-control', 'placeholder' => 'ca-pub-xxxxxxxx']) !!}
  <div class="help-block with-errors"></div>
</div>

<div class="form-group">
  {!! Form::label('ad-slot', 'Google AdSense Slot') !!}
  {!! Form::text('ad-slot', null, ['class' => 'form-control']) !!}
  <div class="help-block with-errors"></div>
</div>
