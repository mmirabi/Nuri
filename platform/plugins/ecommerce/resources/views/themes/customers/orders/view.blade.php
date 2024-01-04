@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    <h2 class="customer-page-title">{{ __('Order information') }}</h2>
    <div class="clearfix"></div>
    <br>

    <div class="customer-order-detail">
        @include('plugins/ecommerce::themes.includes.order-tracking-detail')
        <br>
        <p class="mt-3">
            @if ($order->isInvoiceAvailable())
                <a
                    class="btn btn-success me-1"
                    href="{{ route('customer.print-order', $order->id) }}?type=print"
                    target="_blank"
                >{{ __('Print invoice') }}</a>
                <a
                    class="btn btn-success me-1"
                    href="{{ route('customer.print-order', $order->id) }}"
                >{{ __('Download invoice') }}</a>
            @endif
            @if ($order->canBeCanceled())
                <a
                    class="btn btn-danger me-1"
                    href="{{ route('customer.orders.cancel', $order->id) }}"
                >{{ __('Cancel order') }}</a>
            @endif
            @if ($order->canBeReturned())
                <a
                    class="btn btn-danger me-1"
                    href="{{ route('customer.order_returns.request_view', $order->id) }}"
                >{{ __('Return Product(s)') }}</a>
            @endif
        </p>
    @endsection
