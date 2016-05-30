<?php

use Illuminate\Support\HtmlString;

Html::macro('icon', function ($icon, $fw = false) {
    $fw = $fw ? ' fa-fw' : '';

    $html =<<<EOF
<i class="fa fa-{$icon}{$fw}" aria-hidden="true"></i>
EOF;

    return new HtmlString($html);
});

Html::macro('pagination', function ($data) {
    $pagination = $data->links();

    $html =<<<EOF
<section class="text-center">
  {$pagination}
</section>
EOF;

    return new HtmlString($html);
});

Html::component('formErrors', 'components.formErrors', []);

Html::component('linkButton', 'components.linkButton', [
    'name',
    'parameters' => [],
    'buttonClass' => '',
    'icon' => null,
    'text' => null,
]);
