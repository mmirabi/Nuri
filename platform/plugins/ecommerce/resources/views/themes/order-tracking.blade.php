<section class="order-tracking">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="text-center mb-5">
                <p>{{ __('To track your order please enter your Order ID in the box below and press the "Track" button. This was given to you on your receipt and in the confirmation email you should have received.') }}</p>
            </div>

            {!! $form !!}
            @include('plugins/ecommerce::themes.includes.order-tracking-detail')
        </div>
    </div>
</section>
