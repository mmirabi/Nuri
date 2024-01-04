<?php

namespace Botble\Ecommerce\Forms\Fronts\Auth\FieldOptions;

use Illuminate\Support\Facades\Blade;

trait HasIcon
{
    public function icon(string $name): static
    {
        $this
            ->prepend(Blade::render(
                sprintf('<div class="position-relative"><span class="auth-input-icon input-group-text"><x-core::icon name="%s" /></span>', $name)
            ))
            ->append('</div>')
            ->cssClass('form-control ps-5');

        return $this;
    }
}
