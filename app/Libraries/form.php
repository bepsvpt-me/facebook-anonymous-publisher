<?php

use Illuminate\Support\HtmlString;

Form::macro('validatorHelper', function () {
    return new HtmlString('<div class="help-block with-errors"></div>');
});

Form::macro('submitButton', function ($text = null, $style = 'success', $block = true) {
    $text = is_null($text) ? '送出' : e($text);

    $style = e($style);

    $block = $block ? ' btn-block' : '';

    $html =<<<EOF
<div class="form-group">
  <button type="submit" class="btn btn-{$style}{$block}">{$text}</button>
</div>
EOF;

    return new HtmlString($html);
});
