<?php

namespace Botble\Stripe\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Forms\PaymentMethodForm;
use Illuminate\Support\Facades\Blade;

class StripePaymentMethodForm extends PaymentMethodForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(STRIPE_PAYMENT_METHOD_NAME)
            ->paymentName('Stripe')
            ->paymentDescription(trans('plugins/payment::payment.stripe_description'))
            ->paymentLogo(url('vendor/core/plugins/stripe/images/stripe.svg'))
            ->paymentUrl('https://stripe.com')
            ->paymentInstructions(Blade::render(<<<BLADE
                <ol>
                    <li>
                        <p>
                            <a href="https://dashboard.stripe.com/register" target="_blank">
                                {{ trans('plugins/payment::payment.service_registration', ['name' => 'Stripe']) }}
                            </a>
                        </p>
                    </li>
                    <li>
                        <p>{{ trans('plugins/payment::payment.stripe_after_service_registration_msg', ['name' => 'Stripe']) }}</p>
                    </li>
                    <li>
                        <p>{{ trans('plugins/payment::payment.stripe_enter_client_id_and_secret') }}</p>
                    </li>
                </ol>

                <h4>{{ trans('plugins/stripe::stripe.webhook_setup_guide.title') }}</h4>

                <p>{{ trans('plugins/stripe::stripe.webhook_setup_guide.description') }}</p>

                <ol>
                    <li>
                        <p><strong>{{ trans('plugins/stripe::stripe.webhook_setup_guide.step_1_label') }}:</strong> {!! BaseHelper::clean(trans('plugins/stripe::stripe.webhook_setup_guide.step_1_description', ['link' => '<a href="https://dashboard.stripe.com/" target="_blank">Stripe Dashboard</a>'])) !!}</p>
                    </li>

                    <li>
                        <p><strong>{{ trans('plugins/stripe::stripe.webhook_setup_guide.step_2_label') }}:</strong> {!! BaseHelper::clean(trans('plugins/stripe::stripe.webhook_setup_guide.step_2_description', ['url' => '<code>' . route('payments.stripe.webhook') . '</code>'])) !!}</p>
                    </li>

                    <li>
                        <p><strong>{{ trans('plugins/stripe::stripe.webhook_setup_guide.step_3_label') }}:</strong> {{ trans('plugins/stripe::stripe.webhook_setup_guide.step_3_description') }}</p>
                    </li>

                    <li>
                        <p><strong>{{ trans('plugins/stripe::stripe.webhook_setup_guide.step_4_label') }}:</strong> {{ trans('plugins/stripe::stripe.webhook_setup_guide.step_4_description') }}</p>
                    </li>
                </ol>
            BLADE))
            ->add(
                'payment_stripe_client_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.stripe_key'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('client_id', 'stripe'))
                    ->placeholder('pk_*************')
                    ->attributes(['data-counter' => 400])
                    ->toArray()
            )
            ->add(
                'payment_stripe_secret',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.stripe_secret'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('secret', 'stripe'))
                    ->placeholder('sk_*************')
                    ->toArray()
            )
            ->add(
                'payment_' . STRIPE_PAYMENT_METHOD_NAME . '_payment_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Payment Type'))
                    ->choices([
                        'stripe_api_charge' => 'Stripe API Charge',
                        'stripe_checkout' => 'Stripe Checkout',
                    ])
                    ->selected(get_payment_setting(
                        'payment_type',
                        STRIPE_PAYMENT_METHOD_NAME,
                        'stripe_api_charge',
                    ))
                    ->toArray()
            )
            ->add(
                'payment_stripe_webhook_secret',
                'password',
                TextFieldOption::make()
                    ->label(trans('plugins/stripe::stripe.webhook_secret'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('webhook_secret', 'stripe'))
                    ->placeholder('whsec_*************')
                    ->toArray()
            );
    }
}
