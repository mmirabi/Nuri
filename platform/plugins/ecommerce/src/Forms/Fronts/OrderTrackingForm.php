<?php

namespace Botble\Ecommerce\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Http\Requests\Fronts\OrderTrackingRequest;

class OrderTrackingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->contentOnly()
            ->setMethod('GET')
            ->setValidatorClass(OrderTrackingRequest::class)
            ->setUrl(route('public.orders.tracking'))
            ->add(
                'order_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Order ID'))
                    ->required()
                    ->placeholder(__('Enter the order ID'))
                    ->toArray()
            )
            ->add(
                'email',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Email'))
                    ->required()
                    ->placeholder(__('Enter your email'))
                    ->toArray()
            )
            ->add('submit', 'button', [
                'label' => __('Track'),
                'attr' => [
                    'type' => 'submit',
                    'class' => 'w-100 btn btn-primary',
                ],
            ]);
    }
}
