@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header')
    @include('plugins/ecommerce::discounts.partials.trans')
@endpush

@section('content')
    <x-core::form>
        <discount-component
            currency="{{ get_application_currency()->symbol }}"
            date-format="{{ config('core.base.general.date_format.date') }}"
        ></discount-component>
    </x-core::form>
@stop

@push('footer')
    {!! $jsValidation !!}
@endpush
