<?php

namespace Botble\SocialLogin\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use Botble\SocialLogin\Facades\SocialService;
use Botble\SocialLogin\Http\Requests\Settings\SocialLoginSettingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;

class SocialLoginSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/social-login::social-login.settings.title'))
            ->setSectionDescription(trans('plugins/social-login::social-login.settings.description'))
            ->setValidatorClass(SocialLoginSettingRequest::class);

        $this
            ->add('social_login_enable', 'onOffCheckbox', [
                'label' => trans('plugins/social-login::social-login.settings.enable'),
                'value' => setting('social_login_enable'),
                'attr' => [
                    'data-bb-toggle' => 'collapse',
                    'data-bb-target' => '.social-login-provider-settings',
                ],
            ])
            ->add('open_fieldset_social_login_provider_settings', 'html', [
                'html' => Blade::render(sprintf(
                    '<fieldset class="social-login-provider-settings form-fieldset"
                    data-bb-value="1"
                    style="display: %s"/>',
                    old('social_login_enable', setting('social_login_enable')) ? 'block' : 'none',
                )),
            ]);

        foreach (SocialService::getProviders() as $provider => $item) {
            $this
                ->add($enabledKey = "social_login_{$provider}_enable", 'onOffCheckbox', [
                    'label' =>
                        $item['label']['enable'] ?? trans(sprintf('plugins/social-login::social-login.settings.%s.enable', $provider)),
                    'value' => setting($enabledKey),
                    'attr' => [
                        'data-bb-toggle' => 'collapse',
                        'data-bb-target' => '.' . ($fieldsetKey = "social-login-{$provider}-settings"),
                    ],
                ])
                ->add("open_{$fieldsetKey}", 'html', [
                    'html' => Blade::render(sprintf(
                        '<fieldset class="' . $fieldsetKey . ' form-fieldset"
                    data-bb-value="1"
                    style="display: %s"/>',
                        old($enabledKey, setting($enabledKey)) ? 'block' : 'none',
                    )),
                ]);

            foreach ($item['data'] as $input) {
                $isDisabled = in_array(app()->environment(), SocialService::getEnvDisableData()) && in_array($input, Arr::get($item, 'disable', []));
                $label = $item['label'][$input] ?? trans('plugins/social-login::social-login.settings.' . $provider . '.' . $input);
                $key = 'social_login_' . $provider . '_' . $input;

                $this
                    ->add(
                        $key,
                        TextField::class,
                        TextFieldOption::make()
                            ->label($label)
                            ->value($isDisabled ? SocialService::getDataDisable($provider . '_' . $input) : setting($key))
                            ->disabled($isDisabled)
                            ->toArray()
                    );
            }

            $this->add('social_login_' . $provider . '_' . $input . '_helper', 'html', [
                'html' => Blade::render(
                    '<x-core::alert>' . BaseHelper::clean($item['label']['helper'] ?? trans('plugins/social-login::social-login.settings.' . $provider . '.helper', [
                        'callback' => '<code class=\'text-danger\'>' . route('auth.social.callback', $provider) . '</code>',
                    ])) . '</x-core::alert>'
                ),
            ]);

            $this
                ->add("close_{$fieldsetKey}", 'html', [
                    'html' => '</fieldset>',
                ]);
        }

        $this
            ->add('close_fieldset_social_login_provider_settings', 'html', [
                'html' => '</fieldset>',
            ]);
    }
}
