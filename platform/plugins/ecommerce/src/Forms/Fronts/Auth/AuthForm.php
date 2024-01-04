<?php

namespace Botble\Ecommerce\Forms\Fronts\Auth;

use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\FormAbstract;
use Botble\Theme\Facades\Theme;

abstract class AuthForm extends FormAbstract
{
    public function setup(): void
    {
        Theme::asset()->add('auth-css', 'vendor/core/plugins/ecommerce/css/front-auth.css');

        $this
            ->contentOnly()
            ->template('plugins/ecommerce::customers.forms.auth');
    }

    protected function submitButton(string $label): static
    {
        return $this
            ->add('openButtonWrap', HtmlField::class, [
                'html' => '<div class="d-grid">',
            ])
            ->add('submit', 'submit', [
                'label' => $label,
                'attr' => [
                    'class' => 'btn btn-primary btn-auth-submit',
                ],
            ])
            ->add('closeButtonWrap', HtmlField::class, [
                'html' => '</div>',
            ]);
    }

    protected function banner(string $banner): static
    {
        return $this->setFormOption('banner', $banner);
    }

    protected function icon(string $icon): static
    {
        return $this->setFormOption('icon', $icon);
    }

    protected function heading(string $heading): static
    {
        return $this->setFormOption('heading', $heading);
    }

    protected function description(string $description): static
    {
        return $this->setFormOption('description', $description);
    }
}
