@extends(EcommerceHelper::viewPath('customers.master'))

@section('content')
    <h2 class="customer-page-title">{{ __('Address books') }}</h2>
    <br>
    {!! Form::open(['route' => ['customer.address.edit', $address->id]]) !!}
        @include('plugins/ecommerce::themes.customers.address.form', ['address' => $address])
    {!! Form::close() !!}
@endsection
